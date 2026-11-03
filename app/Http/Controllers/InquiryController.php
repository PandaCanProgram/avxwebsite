<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Jobs\DeliverInquiryEmail;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InquiryController extends Controller
{
    public function store(StoreInquiryRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $details = [
            'kind' => $request->isInvestment() ? 'investment' : 'contact',
            'name' => $validated['name'],
            'email' => $validated['email'],
            'organization' => $validated['organization'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'interest' => $validated['interest'] ?? null,
            'subject' => $request->isInvestment() ? 'Investment inquiry' : $validated['subject'],
            'message' => $validated['message'],
        ];

        $inquiry = DB::transaction(function () use ($validated, $details, $request): Inquiry {
            $inquiry = Inquiry::firstOrCreate(['submission_id' => $validated['submission_id']], $details);

            if (! $inquiry->wasRecentlyCreated) {
                foreach ($details as $field => $value) {
                    if ($inquiry->{$field} !== $value) {
                        throw ValidationException::withMessages([
                            'submission_id' => 'This submission has already been used. Please refresh the page before sending another inquiry.',
                        ])->errorBag('inquiry')->redirectTo($request->returnUrl());
                    }
                }

                return $inquiry;
            }

            DeliverInquiryEmail::dispatch($inquiry->getKey())->beforeCommit();
            DeliverInquiryEmail::dispatch($inquiry->getKey(), true)->beforeCommit();

            return $inquiry;
        });

        $message = 'Thank you! Your inquiry has been received. A confirmation email will follow.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'reference' => $inquiry->reference(),
                'next_submission_id' => (string) Str::uuid(),
            ], $inquiry->wasRecentlyCreated ? 201 : 200);
        }

        return redirect()->to($request->returnUrl())->with('inquirySubmitted', $message.' Reference: '.$inquiry->reference());
    }
}
