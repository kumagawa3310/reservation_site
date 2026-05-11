<?php

namespace App\Http\Controllers;

use App\Models\StayPlan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function createIntent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id'          => 'required|exists:stay_plans,id',
            'check_in'         => 'required|date',
            'check_out'        => 'required|date|after:check_in',
            'number_of_guests' => 'required|integer|min:1',
        ]);

        $plan   = StayPlan::findOrFail($validated['plan_id']);
        $nights = Carbon::parse($validated['check_in'])->diffInDays($validated['check_out']);
        $amount = $plan->price * $nights * $validated['number_of_guests'];

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount'               => $amount,
            'currency'             => 'jpy',
            'payment_method_types' => ['card'],
        ]);

        return response()->json(['client_secret' => $intent->client_secret]);
    }
}
