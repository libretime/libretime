<?php
Class BlockModelData
{
    public static function getCriteriaSingleNewestLabelNada() {

        return array(
            Array("name" => "sp_type", "value" => 0),
            Array("name" => "sp_type", "value" => 0),
            Array("name" => "sp_repeat_tracks", "value" => 0),
            Array("name" => "sp_sort_options", "value" => "newest"),
            Array("name" => "sp_limit_value", "value" => 1),
            Array("name" => "sp_limit_options", "value" => "items"),
            Array("name" => "sp_criteria_field_0_0", "value" => "label"),
            Array("name" => "sp_criteria_modifier_0_0", "value" => "contains"),
            Array("name" => "sp_criteria_value_0_0", "value" => "nada"),
            Array("name" => "sp_overflow_tracks", "value" => 0),
            );
    }


    public static function getCriteriaMultiTrackAndAlbum1Hour()
    {
        return array (
            Array("name" => "sp_type" , "value" => 1),
            Array("name" => "sp_repeat_tracks", "value" => 0),
            Array("name" => "sp_sort_options", "value" => "random"),
            Array("name" => "sp_limit_value", "value" => 1),
            Array("name" => "sp_limit_options", "value" => "hours"),
            Array("name" => "sp_overflow_tracks", "value" => 0),
            Array("name" => "sp_criteria_field_0_0", "value" => "album_title"),
            Array("name" => "sp_criteria_modifier_0_0", "value" => "is"),
            Array("name" => "sp_criteria_value_0_0", "value" => "album1"),
            Array("name" => "sp_criteria_field_0_1", "value" => "album_title"),
            Array("name" => "sp_criteria_modifier_0_1", "value" => "is"),
            Array("name" => "sp_criteria_value_0_1", "value" => "album2"),
            Array("name" => "sp_criteria_field_1_0", "value" => "track_title"),
            Array("name" => "sp_criteria_modifier_1_0", "value" => "is"),
            Array("name" => "sp_criteria_value_1_0", "value" => "track1"),
            Array("name" => "sp_criteria_field_1_1", "value" => "track_title"),
            Array("name" => "sp_criteria_modifier_1_1", "value" => "is"),
            Array("name" => "sp_criteria_value_1_1", "value" => "track2"),
            Array("name" => "sp_criteria_field_1_2", "value" => "track_title"),
            Array("name" => "sp_criteria_modifier_1_2", "value" => "is"),
            Array("name" => "sp_criteria_value_1_2", "value" => "track3"),
            Array("name" => "sp_criteria_field_2_0", "value" => "length"),
            Array("name" => "sp_criteria_modifier_2_0", "value" => "is greater than"),
            Array("name" => "sp_criteria_value_2_0", "value" => "00:01:00"),
        );

    }
}