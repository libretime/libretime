<?php

class Application_Common_Timezone
{
    /**
     * @return mixed[]
     */
    public static function getTimezones(): array
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

        foreach ($regions as $mask) {
            $ids = DateTimeZone::listIdentifiers($mask);
            foreach ($ids as $id) {
                $tzlist[$id] = str_replace('_', ' ', $id);
            }
        }

        return $tzlist;
    }
}
