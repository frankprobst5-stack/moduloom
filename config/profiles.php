<?php

use App\Profiles\PersonalProfile;
use App\Profiles\SmallBusinessProfile;

// Profile ("ecosystem") registry: slug => its manifest class.
return [
    'personal' => PersonalProfile::class,
    'small-business' => SmallBusinessProfile::class,
];
