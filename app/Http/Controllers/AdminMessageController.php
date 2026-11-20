<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveMessageGroupRequest;
use App\Http\Requests\StartDirectMessageRequest;
use App\Http\Requests\StoreGroupMessageRequest;
use App\Models\GroupMessage;
use App\Models\MessageGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminMessageController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin-messages', ['groups' => $this->groups($request->user()), 'group' => null]);
    }

    public function create(): View
    {
        return view('admin-message-group-form', ['group' => null, 'users' => $this->teamMembers()]);
    }

    public function createDirect(): View
    {
        return view('admin-direct-message-form', ['users' => $this->teamMembers()]);
    }

    public function storeDirect(StartDirectMessageRequest $request): RedirectResponse
    {
        $recipient = User::where('is_admin', false)->findOrFail($request->validated('recipient_id'));
        $group = DB::transaction(function () use ($request, $recipient): MessageGroup {
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $group = MessageGroup::where('created_by', $request->user()->id)
                ->where('direct_user_id', $recipient->id)->first();

            if (! $group) {
                $group = new MessageGroup(['name' => $recipient->name]);
                $group->creator()->associate($request->user());
                $group->recipient()->associate($recipient);
                $group->save();
                $group->members()->sync([$request->user()->id, $recipient->id]);
            }

            return $group;
        });

        return redirect()->route('admin.messages.show', $group);
    }

    public function store(SaveMessageGroupRequest $request): RedirectResponse
    {
        $group = DB::transaction(function () use ($request): MessageGroup {
            $group = new MessageGroup($request->safe()->only('name'));
            $group->creator()->associate($request->user());
            $group->save();
            $group->members()->sync([...$request->validated('members'), $request->user()->id]);

            return $group;
        });

        return redirect()->route('admin.messages.show', $group)->with('messageStatus', 'Group created. Send your first message below.');
    }

    public function show(Request $request, MessageGroup $group): View
    {
        abort_unless($group->isOwnedBy($request->user()), 403);

        return view('admin-messages', [
            'groups' => $this->groups($request->user()),
            'group' => $group->load(['members', 'recipient:id,name']),
            ...$this->history($group),
        ]);
    }

    public function edit(Request $request, MessageGroup $group): View
    {
        abort_unless($group->isOwnedBy($request->user()) && ! $group->isDirect(), 403);

        return view('admin-message-group-form', ['group' => $group->load('members'), 'users' => $this->teamMembers()]);
    }

    public function update(SaveMessageGroupRequest $request, MessageGroup $group): RedirectResponse
    {
        DB::transaction(function () use ($request, $group): void {
            $group->fill($request->safe()->only('name'))->save();
            $group->members()->sync([...$request->validated('members'), $group->created_by]);
        });

        return redirect()->route('admin.messages.show', $group)->with('messageStatus', 'Group updated. Conversation history has been kept.');
    }

    public function send(StoreGroupMessageRequest $request, MessageGroup $group): JsonResponse|RedirectResponse
    {
        $message = DB::transaction(function () use ($request, $group): GroupMessage {
            MessageGroup::whereKey($group->id)->lockForUpdate()->firstOrFail();
            $message = $group->messages()->firstOrCreate([
                'user_id' => $request->user()->id,
                'client_id' => $request->validated('client_id'),
            ], ['body' => $request->validated('body')]);

            if ($message->wasRecentlyCreated) {
                $group->touch();
            }

            return $message->load('sender:id,name');
        });

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('partials.admin-chat-messages', ['messages' => collect([$message])])->render(),
                'id' => $message->id,
                'body' => $message->body,
                'next_client_id' => (string) Str::uuid(),
            ]);
        }

        return redirect()->route('admin.messages.show', $group);
    }

    public function feed(Request $request, MessageGroup $group): JsonResponse
    {
        abort_unless($group->isOwnedBy($request->user()), 403);
        $request->validate([
            'after' => ['sometimes', 'integer', 'min:0', 'prohibits:before'],
            'before' => ['sometimes', 'integer', 'min:1', 'prohibits:after'],
        ]);

        if ($request->has('after')) {
            $messages = $group->messages()->with('sender:id,name')
                ->where('id', '>', $request->integer('after'))->orderBy('id')->limit(100)->get();
            $hasMore = $messages->count() === 100;
        } else {
            ['messages' => $messages, 'hasEarlier' => $hasMore] = $this->history(
                $group, $request->has('before') ? $request->integer('before') : null,
            );
        }

        return response()->json([
            'html' => view('partials.admin-chat-messages', compact('messages'))->render(),
            'first_id' => $messages->first()?->id,
            'last_id' => $messages->last()?->id,
            'has_more' => $hasMore,
        ]);
    }

    /** @return LengthAwarePaginator<int, MessageGroup> */
    private function groups(User $user): LengthAwarePaginator
    {
        return MessageGroup::where('created_by', $user->id)->withCount('members')
            ->with(['latestMessage.sender:id,name', 'recipient:id,name'])->orderByDesc('updated_at')->orderByDesc('id')->paginate(20);
    }

    /** @return Collection<int, User> */
    private function teamMembers(): Collection
    {
        return User::where('is_admin', false)->orderBy('name')->orderBy('id')->get(['id', 'name', 'email', 'position']);
    }

    /** @return array{messages: Collection<int, GroupMessage>, hasEarlier: bool} */
    private function history(MessageGroup $group, ?int $before = null): array
    {
        $messages = $group->messages()->with('sender:id,name')->when($before !== null,
            fn (Builder $query): Builder => $query->where('id', '<', $before))
            ->orderByDesc('id')->limit(51)->get();

        return ['messages' => $messages->take(50)->reverse()->values(), 'hasEarlier' => $messages->count() > 50];
    }
}
