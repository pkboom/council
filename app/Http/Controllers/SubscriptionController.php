<?php

namespace App\Http\Controllers;

use App\Registration\RegistersLifetimeMember;
use App\Registration\RegistersTeamMember;
use App\Registration\RegistersSubscriber;
use App\Registration\RegistersForumUser;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    // 1. Identify a point of flexibility
    // 1) Forum Account
    // 2) Regular Subscriber
    // 3) Team Member Access
    // 4) Forever Account

    // 2. Extract each strategy into its own class
    // 3. Ensure that each of those strategies adheres to a common/interface
    // 4. Determine the proper strategy, and new it up and let it handle the task.

    public function store()
    {
        $this->getRegistrationStrategy()->handle(request()->all());
    }

    public function getRegistrationStrategy()
    {
        if (request()->plan == 'forever') {
            return new RegistersLifetimeMember;
        }

        if (request()->plan == 'forum') {
            return new RegistersForumUser;
        }

        if (request()->invite) {
            return new RegistersTeamMember;
        }

        return new RegistersSubscriber;
    }

    // The value might be in the proper format from the start.
    // But it could also be in the form of a function that you need to trigger.
    // Or you need to modify the value before continuing on.
    // One option is extracting all of the normalizing-specific code into either its own method, or a different class entirely.

    public function update(Request $request)
    {
        $code = $request->code;
        $plan = $request->plan;

        $coupon = Coupon::normalize($code)->against($plan);

        $this->user
            ->subscription()
            ->usingCoupon($coupon)
            ->swap($plan);
    }
}
