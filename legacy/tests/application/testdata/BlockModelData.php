<?php

class BlockModelData
{
    public static function getCriteriaSingleNewestLabelNada()
    {
        return [
            ['name' => 'sp_type', 'value' => 0],
            ['name' => 'sp_type', 'value' => 0],
            ['name' => 'sp_repeat_tracks', 'value' => 0],
            ['name' => 'sp_sort_options', 'value' => 'newest'],
            ['name' => 'sp_limit_value', 'value' => 1],
            ['name' => 'sp_limit_options', 'value' => 'items'],
            ['name' => 'sp_criteria_field_0_0', 'value' => 'label'],
            ['name' => 'sp_criteria_modifier_0_0', 'value' => 'contains'],
            ['name' => 'sp_criteria_value_0_0', 'value' => 'nada'],
            ['name' => 'sp_overflow_tracks', 'value' => 0],
        ];
    }

    public static function getCriteriaMultiTrackAndAlbum1Hour()
    {
        return [
            ['name' => 'sp_type', 'value' => 1],
            ['name' => 'sp_repeat_tracks', 'value' => 0],
            ['name' => 'sp_sort_options', 'value' => 'random'],
            ['name' => 'sp_limit_value', 'value' => 1],
            ['name' => 'sp_limit_options', 'value' => 'hours'],
            ['name' => 'sp_overflow_tracks', 'value' => 0],
            ['name' => 'sp_criteria_field_0_0', 'value' => 'album_title'],
            ['name' => 'sp_criteria_modifier_0_0', 'value' => 'is'],
            ['name' => 'sp_criteria_value_0_0', 'value' => 'album1'],
            ['name' => 'sp_criteria_field_0_1', 'value' => 'album_title'],
            ['name' => 'sp_criteria_modifier_0_1', 'value' => 'is'],
            ['name' => 'sp_criteria_value_0_1', 'value' => 'album2'],
            ['name' => 'sp_criteria_field_1_0', 'value' => 'track_title'],
            ['name' => 'sp_criteria_modifier_1_0', 'value' => 'is'],
            ['name' => 'sp_criteria_value_1_0', 'value' => 'track1'],
            ['name' => 'sp_criteria_field_1_1', 'value' => 'track_title'],
            ['name' => 'sp_criteria_modifier_1_1', 'value' => 'is'],
            ['name' => 'sp_criteria_value_1_1', 'value' => 'track2'],
            ['name' => 'sp_criteria_field_1_2', 'value' => 'track_title'],
            ['name' => 'sp_criteria_modifier_1_2', 'value' => 'is'],
            ['name' => 'sp_criteria_value_1_2', 'value' => 'track3'],
            ['name' => 'sp_criteria_field_2_0', 'value' => 'length'],
            ['name' => 'sp_criteria_modifier_2_0', 'value' => 'is greater than'],
            ['name' => 'sp_criteria_value_2_0', 'value' => '00:01:00'],
        ];
    }

    /**
     * Description contains alpha AND (Description contains beta OR Description contains gamma).
     *
     * @param bool $orGroupFirst place the OR group before the single criteria
     */
    public static function getCriteriaDescriptionAndOrGroup($orGroupFirst = false)
    {
        $andGroup = static fn ($i) => [
            ['name' => "sp_criteria_field_{$i}_0", 'value' => 'description'],
            ['name' => "sp_criteria_modifier_{$i}_0", 'value' => 'contains'],
            ['name' => "sp_criteria_value_{$i}_0", 'value' => 'alpha'],
        ];
        $orGroup = static fn ($i) => [
            ['name' => "sp_criteria_field_{$i}_0", 'value' => 'description'],
            ['name' => "sp_criteria_modifier_{$i}_0", 'value' => 'contains'],
            ['name' => "sp_criteria_value_{$i}_0", 'value' => 'beta'],
            ['name' => "sp_criteria_field_{$i}_1", 'value' => 'description'],
            ['name' => "sp_criteria_modifier_{$i}_1", 'value' => 'contains'],
            ['name' => "sp_criteria_value_{$i}_1", 'value' => 'gamma'],
        ];

        return array_merge(
            [
                ['name' => 'sp_type', 'value' => 0],
                ['name' => 'sp_repeat_tracks', 'value' => 0],
                ['name' => 'sp_sort_options', 'value' => 'oldest'],
                ['name' => 'sp_limit_value', 'value' => 10],
                ['name' => 'sp_limit_options', 'value' => 'items'],
                ['name' => 'sp_overflow_tracks', 'value' => 0],
            ],
            $orGroupFirst
                ? array_merge($orGroup(0), $andGroup(1))
                : array_merge($andGroup(0), $orGroup(1))
        );
    }
}
