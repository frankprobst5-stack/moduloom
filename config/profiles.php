<?php

use App\Profiles\CommunityProfile;
use App\Profiles\EducationProfile;
use App\Profiles\EnterpriseProfile;
use App\Profiles\GovernmentProfile;
use App\Profiles\PersonalProfile;
use App\Profiles\SmallBusinessProfile;

// Profile ("ecosystem") registry: slug => its manifest class.
return [
    'personal' => PersonalProfile::class,
    'small-business' => SmallBusinessProfile::class,
    'community' => CommunityProfile::class,
    'education' => EducationProfile::class,
    'enterprise' => EnterpriseProfile::class,
    'government' => GovernmentProfile::class,
];
