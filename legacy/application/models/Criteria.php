<?php

class ModifierType
{
    public const STRING = 's';
    public const NUMBER = 'n';
    public const DATE = 'd';
    public const TRACK_TYPE = 'tt';
}

class CriteriaModifier
{
    public const CONTAINS = 'contains';
    public const DOES_NOT_CONTAIN = 'does not contain';
    public const IS = 'is';
    public const IS_NOT = 'is not';
    public const STARTS_WITH = 'starts with';
    public const ENDS_WITH = 'ends with';
    public const BEFORE = 'before';
    public const AFTER = 'after';
    public const BETWEEN = 'between';
    public const IS_GREATER_THAN = 'is greater than';
    public const IS_LESS_THAN = 'is less than';
    public const IS_IN_THE_RANGE = 'is in the range';

    public static function mapToDisplay(array $modifiers = self::ALL): array
    {
        $arr = ['0' => _('Select modifier')];

        foreach ($modifiers as $m) {
            $arr[$m] = _($m);
        }

        return $arr;
    }

    public const ALL = [
        CriteriaModifier::CONTAINS,
        CriteriaModifier::DOES_NOT_CONTAIN,
        CriteriaModifier::IS,
        CriteriaModifier::IS_NOT,
        CriteriaModifier::STARTS_WITH,
        CriteriaModifier::ENDS_WITH,
        CriteriaModifier::BEFORE,
        CriteriaModifier::AFTER,
        CriteriaModifier::BETWEEN,
        CriteriaModifier::IS_GREATER_THAN,
        CriteriaModifier::IS_LESS_THAN,
        CriteriaModifier::IS_IN_THE_RANGE,
    ];

    public const FOR_STRING = [
        CriteriaModifier::CONTAINS,
        CriteriaModifier::DOES_NOT_CONTAIN,
        CriteriaModifier::IS,
        CriteriaModifier::IS_NOT,
        CriteriaModifier::STARTS_WITH,
        CriteriaModifier::ENDS_WITH,
    ];

    public const FOR_NUMBER = [
        CriteriaModifier::IS,
        CriteriaModifier::IS_NOT,
        CriteriaModifier::IS_GREATER_THAN,
        CriteriaModifier::IS_LESS_THAN,
        CriteriaModifier::IS_IN_THE_RANGE,
    ];

    public const FOR_DATE = [
        CriteriaModifier::BEFORE,
        CriteriaModifier::AFTER,
        CriteriaModifier::BETWEEN,
        CriteriaModifier::IS,
        CriteriaModifier::IS_NOT,
        CriteriaModifier::IS_GREATER_THAN,
        CriteriaModifier::IS_LESS_THAN,
        CriteriaModifier::IS_IN_THE_RANGE,
    ];

    public const FOR_TRACK_TYPE = [
        CriteriaModifier::IS,
        CriteriaModifier::IS_NOT,
    ];
}

class BlockCriteria
{
    public string $key;
    public string $type;
    public string $peer;
    public string $display;

    public function __construct(string $key, string $type, string $peer, string $display)
    {
        $this->key = $key;
        $this->type = $type;
        $this->peer = $peer;
        $this->display = $display;
    }

    public function getModifiers(): array
    {
        $modifiers = [];

        switch ($this->type) {
            case ModifierType::STRING:
                $modifiers = CriteriaModifier::FOR_STRING;

                break;

            case ModifierType::DATE:
                $modifiers = CriteriaModifier::FOR_DATE;

                break;

            case ModifierType::NUMBER:
                $modifiers = CriteriaModifier::FOR_NUMBER;

                break;

            case ModifierType::TRACK_TYPE:
                $modifiers = CriteriaModifier::FOR_TRACK_TYPE;

                break;
        }

        return $modifiers;
    }

    public function displayModifiers(): array
    {
        return CriteriaModifier::mapToDisplay(self::getModifiers());
    }

    private static array $allCriteria;

    /**
     * After adding a new criteria don't forget to also add it into smart_blockbuilder.js.
     *
     * @return BlockCriteria[]
     */
    public static function allCriteria(): array
    {
        if (!isset(BlockCriteria::$allCriteria)) {
            BlockCriteria::$allCriteria = [
                new BlockCriteria('album_title', ModifierType::STRING, 'DbAlbumTitle', _('Album')),
                new BlockCriteria('artist_name', ModifierType::STRING, 'DbArtistName', _('Creator')),
                new BlockCriteria('bit_rate', ModifierType::NUMBER, 'DbBitRate', _('Bit Rate (Kbps)')),
                new BlockCriteria('bpm', ModifierType::NUMBER, 'DbBpm', _('BPM')),
                new BlockCriteria('composer', ModifierType::STRING, 'DbComposer', _('Composer')),
                new BlockCriteria('conductor', ModifierType::STRING, 'DbConductor', _('Conductor')),
                new BlockCriteria('copyright', ModifierType::STRING, 'DbCopyright', _('Copyright')),
                new BlockCriteria('cuein', ModifierType::NUMBER, 'DbCuein', _('Cue In')),
                new BlockCriteria('cueout', ModifierType::NUMBER, 'DbCueout', _('Cue Out')),
                new BlockCriteria('description', ModifierType::STRING, 'DbDescription', _('Description')),
                new BlockCriteria('encoded_by', ModifierType::STRING, 'DbEncodedBy', _('Encoded By')),
                new BlockCriteria('utime', ModifierType::DATE, 'DbUtime', _('Uploaded')),
                new BlockCriteria('mtime', ModifierType::DATE, 'DbMtime', _('Last Modified')),
                new BlockCriteria('lptime', ModifierType::DATE, 'DbLPtime', _('Last Played')),
                new BlockCriteria('genre', ModifierType::STRING, 'DbGenre', _('Genre')),
                new BlockCriteria('info_url', ModifierType::STRING, 'DbInfoUrl', _('Website')),
                new BlockCriteria('isrc_number', ModifierType::STRING, 'DbIsrcNumber', _('ISRC')),
                new BlockCriteria('label', ModifierType::STRING, 'DbLabel', _('Label')),
                new BlockCriteria('language', ModifierType::STRING, 'DbLanguage', _('Language')),
                new BlockCriteria('length', ModifierType::NUMBER, 'DbLength', _('Length')),
                new BlockCriteria('mime', ModifierType::STRING, 'DbMime', _('Mime')),
                new BlockCriteria('mood', ModifierType::STRING, 'DbMood', _('Mood')),
                new BlockCriteria('owner_id', ModifierType::STRING, 'DbOwnerId', _('Owner')),
                new BlockCriteria('replay_gain', ModifierType::NUMBER, 'DbReplayGain', _('Replay Gain')),
                new BlockCriteria('sample_rate', ModifierType::NUMBER, 'DbSampleRate', _('Sample Rate (kHz)')),
                new BlockCriteria('track_title', ModifierType::STRING, 'DbTrackTitle', _('Title')),
                new BlockCriteria('track_number', ModifierType::NUMBER, 'DbTrackNumber', _('Track Number')),
                new BlockCriteria('year', ModifierType::NUMBER, 'DbYear', _('Year')),
                new BlockCriteria('track_type_id', ModifierType::TRACK_TYPE, 'DbTrackTypeId', _('Track Type')),
                new BlockCriteria('filepath', ModifierType::STRING, 'DbFilepath', _('File Name')),
            ];
        }

        return BlockCriteria::$allCriteria;
    }

    public static function displayCriteria(): array
    {
        $arr = [0 => _('Select criteria')];

        foreach (self::allCriteria() as $c) {
            $arr[$c->key] = $c->display;
        }

        return $arr;
    }

    /**
     * @return BlockCriteria[]
     */
    public static function criteriaMap(): array
    {
        $arr = [];
        foreach (self::allCriteria() as $i) {
            $arr[$i->key] = $i;
        }

        return $arr;
    }

    public static function get(string $key): BlockCriteria
    {
        return self::criteriaMap()[$key];
    }
}
