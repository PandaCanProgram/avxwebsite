<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlphaRegistrationRequest;
use App\Jobs\DeliverAlphaRegistrationEmail;
use App\Models\AlphaRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AlphaRegistrationController extends Controller
{
    public function __invoke(StoreAlphaRegistrationRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        DB::transaction(function () use ($validated): void {
            $registration = AlphaRegistration::firstOrCreate(
                ['email' => $validated['alpha_email']],
                ['name' => $validated['alpha_name'], 'consented_at' => now()],
            );

            if ($registration->wasRecentlyCreated) {
                DeliverAlphaRegistrationEmail::dispatch($registration->getKey())->beforeCommit();
            }
        });

        $message = "You're on the list! We'll contact you when an alpha test is ready.";

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->to($request->returnUrl())->with('alphaRegistrationSubmitted', $message);
    }
}
