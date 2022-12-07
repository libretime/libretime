<?php

declare(strict_types=1);

class Application_Common_Timezone
{
    public static function getTimezones()
    {
        $regions = [
            'Africa' => DateTimeZone::AFRICA,
            'America' => DateTimeZone::AMERICA,
            'Antarctica' => DateTimeZone::ANTARCTICA,
            'Arctic' => DateTimeZone::ARCTIC,
            'Asia' => DateTimeZone::ASIA,
            'Atlantic' => DateTimeZone::ATLANTIC,
            'Australia' => DateTimeZone::AUSTRALIA,
            'Europe' => DateTimeZone::EUROPE,
            'Indian' => DateTimeZone::INDIAN,
            'Pacific' => DateTimeZone::PACIFIC,
            'UTC' => DateTimeZone::UTC,
        ];

        $tzlist = [null => _('Use station default')];

        foreach ($regions as $name => $mask) {
            $ids = DateTimeZone::listIdentifiers($mask);
            foreach ($ids as $id) {
                $tzlist[$id] = str_replace('_', ' ', $id);
            }
        }

        return $tzlist;
    }
}
