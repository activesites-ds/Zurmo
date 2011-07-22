<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2011 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
     * details.
     *
     * You should have received a copy of the GNU General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 113 McHenry Road Suite 207,
     * Buffalo Grove, IL 60089, USA. or at email address contact@zurmo.com.
     ********************************************************************************/

    class Meeting extends MashableActivity
    {
        public static function getByName($name)
        {
            assert('is_string($name) && $name != ""');
            return self::getSubset(null, null, null, "name = '$name'");
        }

        public function __toString()
        {
            if (trim($this->name) == '')
            {
                return yii::t('Default', '(Unnamed)');
            }
            return $this->name;
        }

        public static function getModuleClassName()
        {
            return 'MeetingsModule';
        }

        /**
         * Returns the display name for the model class.
         * @return dynamic label name based on module.
         */
        protected static function getLabel()
        {
            return 'MeetingsModuleSingularLabel';
        }

        /**
         * Returns the display name for plural of the model class.
         * @return dynamic label name based on module.
         */
        protected static function getPluralLabel()
        {
            return 'MeetingsModulePluralLabel';
        }

        public static function canSaveMetadata()
        {
            return true;
        }

        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    'name',
                    'startDateTime',
                    'endDateTime',
                    'location',
                    'description',
                ),
                'rules' => array(
                    array('name',             'required'),
                    array('name',             'type',    'type' => 'string'),
                    array('name',             'length',  'min'  => 3, 'max' => 64),
                    array('location',         'type',    'type' => 'string'),
                    array('location',         'length',  'min'  => 3, 'max' => 64),
                    array('startDateTime',    'required'),
                    array('startDateTime',    'type', 'type' => 'datetime'),
                    array('startDateTime',    'RedBeanModelCompareDateTimeValidator', 'type' => 'before',
                                              'compareAttribute' => 'endDateTime'),
                    array('endDateTime',      'type', 'type' => 'datetime'),
                    array('endDateTime',      'RedBeanModelCompareDateTimeValidator', 'type' => 'after',
                                              'compareAttribute' => 'startDateTime'),
                    array('description',      'type', 'type' => 'string'),
                ),
                'relations' => array(
                    'category'             => array(RedBeanModel::HAS_ONE, 'OwnedCustomField', RedBeanModel::OWNED),
                ),
                'elements' => array(
                    'startDateTime' => 'DateTime',
                    'endDateTime'   => 'DateTime',
                ),
                'customFields' => array(
                    'category'     => 'MeetingCategories',
                ),
                'defaultSortAttribute' => 'name',
                'noAudit' => array(
                    'description'
                ),
            );
            return $metadata;
        }

        protected function untranslatedAttributeLabels()
        {
            return array_merge(parent::untranslatedAttributeLabels(),
                array(
                    'startDateTime' => 'Start Time',
                    'endDateTime'   => 'End Time',
                )
            );
        }

        public static function isTypeDeletable()
        {
            return true;
        }

        public static function getMashableActivityRulesType()
        {
            return 'Meeting';
        }

        protected function beforeSave()
        {
            if (parent::beforeSave())
            {
                if (array_key_exists('startDateTime', $this->originalAttributeValues) &&
                    $this->startDateTime != null)
                {
                    $this->unrestrictedSet('latestDateTime', $this->startDateTime);
                }
                return true;
            }
            else
            {
                return false;
            }
        }

        public static function hasReadPermissionsOptimization()
        {
            return true;
        }
    }
?>