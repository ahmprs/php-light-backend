<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";
require_once "$srv/lib/user.php";
require_once "$srv/lib/dbs.php";
require_once "$srv/lib/calendar.php";

class Survey
{
    public static function makeSurvey()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $survey_title = par('survey_title');
        if ($survey_title == '') {
            return resp(0, [
                'err' => 'missing survey_title',
            ]);
        }

        $survey_post_id = par('survey_post_id');
        if ($survey_post_id == '') {
            $survey_post_id = 0;
        }

        $survey_text = par('survey_text');
        if ($survey_text == '') {
            return resp(0, [
                'err' => 'missing survey_text',
            ]);
        }
        $survey_desc = par('survey_desc');

        $expires_in_days = par('expires_in_days');
        if ($expires_in_days == '') {
            $expires_in_days = 7;
        } else {
            try {
                $expires_in_days = (int) $expires_in_days;
            }
            //
             catch (\Throwable $th) {
                $expires_in_days = 7;
            }
        }

        $c = new Calendar();
        $survey_create_gdp = $c->get_server_gdp_time();
        $survey_expire_gdp = $survey_create_gdp + $expires_in_days;

        try {
            $r = DBS::insert(
                "INSERT INTO `tbl_survey` (`survey_id`, `survey_owner_id`, `survey_audit_id`, `survey_post_id`, `survey_title`, `survey_text`, `survey_create_gdp`, `survey_expire_gdp`, `publish`, `survey_desc`) VALUES (NULL, ?, 0, 0, ?, ?, ?, ?, 0, '')",
                'issdd',
                [
                    $user_id,
                    $survey_title,
                    $survey_text,
                    $survey_create_gdp,
                    $survey_expire_gdp,
                ]
            );
            if ($r['affected_rows_count'] == 0) {
                return resp(0, 'making new survey failed.');
            }
            return resp(1, [
                'msg' => '',
                'survey_id' => $r['record_id'],
                'inf' => $r,
            ]);
        }
        //
         catch (\Throwable $th) {
            return resp(0, 'unable to finish the process of making survey');
        }
    }
    public static function removeSurvey()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $survey_id = par('survey_id');
        if ($survey_id == '') {
            return resp(0, [
                'err' => 'missing survey_id',
            ]);
        }
        if (!Survey::surveyExists($survey_id)) {
            return resp(0, [
                'err' => 'invalid survey_id',
            ]);
        }

        if (!Survey::isSurveyOwner($user_id, $survey_id)) {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'only survey owner can remove a survey',
            ]);
        }

        $r = Survey::surveyDelete($survey_id);
        if ($r == 0) {
            return resp(0, [
                'err' => 'survey delete failed',
            ]);
        }
        return resp(1, [
            'msg' => 'survey delete succeeded',
        ]);

    }
    public static function addChoice()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $survey_id = par('survey_id');
        if ($survey_id == '') {
            return resp(0, [
                'err' => 'missing survey_id',
            ]);
        }
        if (!Survey::surveyExists($survey_id)) {
            return resp(0, [
                'err' => 'invalid survey_id',
                'survey_id' => $survey_id,
            ]);
        }

        if (!Survey::isSurveyOwner($user_id, $survey_id)) {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'only survey owner can remove a survey',
            ]);
        }

        $choice_text = par('choice_text');
        if ($choice_text == '') {
            return resp(0, [
                'err' => 'missing choice_text',
            ]);
        }

        try {
            $r = DBS::insert(
                "INSERT INTO `tbl_survey_choices` (`choice_id`, `survey_id`, `choice_text`, `choice_count`) VALUES (NULL, ?, ?, 0)",
                'is',
                [
                    $survey_id,
                    $choice_text,
                ]
            );
            if ($r['affected_rows_count'] != 1) {
                return resp(0, [
                    'err' => 'add choice failed',
                ]);
            }
            return resp(1, [
                'msg' => 'add choice succeeded',
            ]);
        }
        //
         catch (\Throwable $th) {
            return resp(0, [
                'err' => 'unable to finish add choice process',
            ]);
        }
    }
    public static function removeChoice()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $survey_id = par('survey_id');
        if ($survey_id == '') {
            return resp(0, [
                'err' => 'missing survey_id',
            ]);
        }

        $choice_text = par('choice_text');
        if ($choice_text == '') {
            return resp(0, [
                'err' => 'missing choice_text',
            ]);
        }

        if (!Survey::surveyExists($survey_id)) {
            return resp(0, [
                'err' => 'invalid survey_id',
                'survey_id' => $survey_id,
            ]);
        }

        if (!Survey::isSurveyOwner($user_id, $survey_id)) {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'only survey owner can remove a survey',
            ]);
        }

        try {
            $r = DBS::delete(
                "delete from `tbl_survey_choices` where survey_id=? and choice_text=?",
                'is',
                [
                    $survey_id,
                    $choice_text,
                ]
            );
            if ($r['affected_rows_count'] == 0) {
                return resp(0, [
                    'err' => 'remove choice failed',
                ]);
            }

            return resp(1, [
                'msg' => 'remove choice succeeded',
            ]);
        }
        //
         catch (\Throwable $th) {
            return resp(0, [
                'err' => 'unable to finish add choice process',
            ]);
        }

    }
    public static function publishSurvey($publish = 1)
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $survey_id = par('survey_id');
        if ($survey_id == '') {
            return resp(0, [
                'err' => 'missing survey_id',
            ]);
        }

        if (!Survey::surveyExists($survey_id)) {
            return resp(0, [
                'err' => 'invalid survey_id',
                'survey_id' => $survey_id,
            ]);
        }

        if (!Survey::isSurveyOwner($user_id, $survey_id)) {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'only survey owner has access to publish or hide the survey',
            ]);
        }

        $r = DBS::update(
            "update tbl_survey set publish=? where survey_id=?",
            'ii',
            [
                $publish,
                $survey_id,
            ]
        );
        if ($r['affected_rows_count'] == 0) {
            return resp(0, 'publish survey failed');
        }
        return resp(0, 'publish survey succeeded');
    }
    public static function hideSurvey()
    {
        Survey::publishSurvey(0);
    }

    public static function vote()
    {
        $survey_id = par('survey_id');
        if ($survey_id == '') {
            return resp(0, [
                'err' => 'missing survey_id',
            ]);
        }

        if (!Survey::surveyExists($survey_id)) {
            return resp(0, [
                'err' => 'invalid survey_id',
                'survey_id' => $survey_id,
            ]);
        }

        $choice_id = par('choice_id');
        if ($choice_id == '') {
            return resp(0, [
                'err' => 'missing choice_id',
            ]);
        }

        $r = DBS::update(
            "update tbl_survey_choices set choice_count=choice_count+1 where choice_id=? and survey_id=?",
            'ii',
            [
                $choice_id,
                $survey_id,
            ]
        );
        if ($r['affected_rows_count'] == 0) {
            return resp(0, 'vote failed');
        }
        return resp(1, 'vote saved');
    }

    public static function report()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $survey_id = par('survey_id');
        if ($survey_id == '') {
            return resp(0, [
                'err' => 'missing survey_id',
            ]);
        }

        if (!Survey::surveyExists($survey_id)) {
            return resp(0, [
                'err' => 'invalid survey_id',
                'survey_id' => $survey_id,
            ]);
        }

        $r = DBS::select(
            "select * from tbl_survey where survey_id=?",
            'i',
            [
                $survey_id,
            ]
        );

        $p = DBS::select(
            "select * from tbl_survey_choices where survey_id=?",
            'i',
            [
                $survey_id,
            ]
        );

        return resp(1, [
            'survey' => $r['records'],
            'choices' => $p['records'],
        ]);
    }

    private static function isSurveyOwner($user_id, $survey_id)
    {
        try {
            $r = DBS::select(
                "select count(survey_id) as cnt from tbl_survey where survey_owner_id=? and survey_id=?",
                'ii',
                [
                    $user_id,
                    $survey_id,
                ]
            );
            return ($r['records'][0]['cnt'] == 1);
        }
        //
         catch (\Throwable $th) {
            return false;
        }
    }

    private static function surveyExists($survey_id)
    {
        try {
            $r = DBS::select(
                "select survey_id from tbl_survey where survey_id=?",
                'i',
                [
                    $survey_id,
                ]
            );
            return (count($r['records']) == 1);
        }
        //
         catch (\Throwable $th) {
            return false;
        }
    }

    private static function surveyDelete($survey_id)
    {
        try {
            $r = DBS::delete(
                "delete from tbl_survey where survey_id=?",
                'i',
                [
                    $survey_id,
                ]
            );
            return $r['affected_rows_count'];
        }
        //
         catch (\Throwable $th) {
            return 0;
        }
    }

}
