<?php

namespace CauseLabs\SocialiteProviders\IDEO;

use SocialiteProviders\Manager\SocialiteWasCalled;

class IDEOExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('ideo', __NAMESPACE__.'\Provider');
    }
}
