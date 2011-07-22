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

    class PostUtilTest extends BaseTest
    {
        public function testSanitizePostByDesignerTypeForSavingModel()
        {
            $language = Yii::app()->getLanguage();
            $this->assertEquals($language, 'en');
            $postData = array(
                'aDate' => '5/4/11',
                'aDateTime' => '5/4/11 5:45 PM'
            );
            $sanitizedPostData = PostUtil::sanitizePostByDesignerTypeForSavingModel(new DateDateTime(), $postData);
            $compareData = array(
                'aDate' => '2011-05-04',
                'aDateTime' => DateTimeUtil::convertDateTimeLocaleFormattedDisplayToDbFormattedDateTimeWithSecondsAsZero('5/4/11 5:45 PM'),
            );
            $this->assertEquals($compareData, $sanitizedPostData);
            $this->assertTrue(is_string($compareData['aDateTime']));

            //now do German (de) to check a different locale.
            Yii::app()->setLanguage('de');
            $postData = array(
                'aDate' => '04.05.11',
                'aDateTime' => '04.05.11 17:45'
            );
            $sanitizedPostData = PostUtil::sanitizePostByDesignerTypeForSavingModel(new DateDateTime(), $postData);
            $compareData = array(
                'aDate' => '2011-05-04',
                'aDateTime' => DateTimeUtil::convertDateTimeLocaleFormattedDisplayToDbFormattedDateTimeWithSecondsAsZero('04.05.11 17:45'),
            );
            $this->assertEquals($compareData, $sanitizedPostData);
            $this->assertTrue(is_string($compareData['aDateTime']));

            //reset language back to english
            Yii::app()->setLanguage('en');

            //test sanitizing a bad datetime
            $sanitizedPostData = PostUtil::sanitizePostByDesignerTypeForSavingModel(new DateDateTime(),
                                                                                    array('aDateTime' => 'wang chung'));
            $this->assertNull($sanitizedPostData['aDateTime']);
            //sanitize an empty datetime
            $sanitizedPostData = PostUtil::sanitizePostByDesignerTypeForSavingModel(new DateDateTime(),
                                                                                    array('aDateTime' => ''));
            $this->assertEmpty($sanitizedPostData['aDateTime']);
        }

        public function testSanitizePostDataToJustHavingElementForSavingModel()
        {
            $data = array('a' => 'aaa', 'b' => 'bbb', 'c' => 'ccc');
            $newData = PostUtil::sanitizePostDataToJustHavingElementForSavingModel($data, 'nothere');
            $this->assertNull($newData);
            $newData = PostUtil::sanitizePostDataToJustHavingElementForSavingModel($data, 'b');
            $this->assertEquals(array('b' => 'bbb'), $newData);
        }

        public function testRemoveElementFromPostDataForSavingModel()
        {
            $data = array('a' => 'aaa', 'b' => 'bbb', 'c' => 'ccc');
            $newData = PostUtil::removeElementFromPostDataForSavingModel($data, 'doesntexist');
            $this->assertEquals($data, $newData);
            $newData = PostUtil::removeElementFromPostDataForSavingModel($data, 'b');
            $this->assertEquals(array('a' => 'aaa', 'c' => 'ccc'), $newData);
        }
    }
?>