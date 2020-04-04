<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";
require_once "$srv/lib/user.php";
require_once "$srv/lib/dbs.php";
require_once "$srv/lib/calendar.php";

class Chat
{
    public static function makeChatSession()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $chat_session_title = par('chat_session_title');
        $c = new Calendar();
        $gdp = $c->get_server_gdp_time();

        if ($chat_session_title == '') {
            $stamp = $c->getStamp();
            $chat_session_title = $stamp;
        }

        try {
            $r = DBS::insert(
                "INSERT INTO `tbl_chat_sessions` (`chat_session_id`, `chat_session_title`, `chat_session_start_gdp`, `chat_session_dismissed_gdp`, `chat_starter_user_id`) VALUES (NULL, ?, ?, NULL, ?)",
                'sdi',
                [
                    $chat_session_title,
                    $gdp,
                    $user_id,
                ]
            );
            if ($r['affected_rows_count'] == 0) {
                return resp(0, 'make new chat session failed');
            }

            $chat_session_id = $r['record_id'];
            // add the first member to the chat session
            $p = DBS::insert(
                "INSERT INTO `tbl_chat_members` (`rec_id`, `chat_session_id`, `chat_member_user_id`, `chat_member_inviter_id`, `chat_member_invitation_gdp`, `chat_member_blocked`) VALUES (NULL, ?, ?, ?, ?, '0')",
                'iiid',
                [
                    $chat_session_id,
                    $user_id,
                    $user_id,
                    $gdp,
                ]
            );

            if ($p['affected_rows_count'] == 0) {
                return resp(1, [
                    'err' => 'a new chat session is created, however system was unsuccessful to add you to its member list',
                    'chat_session_id' => $r['record_id'],
                ]);

            }
            return resp(1, [
                'msg' => 'make new chat session success',
                'chat_session_id' => $r['record_id'],
            ]);

        }
        //
         catch (\Throwable $th) {
            return resp(0, 'unable to make a new chat session');
        }
    }

    public static function dismissChatSession()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $chat_session_id = par('chat_session_id');
        if ($chat_session_id == '') {
            return resp(0, 'missing chat_session_id');
        }

        $c = new Calendar();
        $gdp = $c->get_server_gdp_time();

        try {
            $r = DBS::update(
                "update `tbl_chat_sessions` set chat_session_dismissed_gdp=? where chat_session_id=?",
                'di',
                [
                    $gdp,
                    $chat_session_id,
                ]
            );
            if ($r['affected_rows_count'] == 1) {
                return resp(1, [
                    'msg' => 'chat session dismissed successfully',
                ]);
            }
            //
            else {
                return resp(0, 'chat session dismiss process failed');
            }
        }
        //
         catch (\Throwable $th) {
            return resp(0, 'unable to dismiss chat session');
        }

    }
    public static function addMember()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $chat_session_id = par('chat_session_id');
        if ($chat_session_id == '') {
            return resp(0, 'missing chat_session_id');
        }

        $chat_member_user_id = par('chat_member_user_id');
        if ($chat_member_user_id == '') {
            return resp(0, 'missing chat_member_user_id');
        }

        $c = new Calendar();
        $gdp = $c->get_server_gdp_time();

        try {

            // check if the requested chat session exists
            $r = DBS::select(
                "select count(chat_session_id) from tbl_chat_sessions where chat_session_id=?",
                'i',
                [
                    $chat_session_id,
                ]
            );
            $cnt = count($r['records']);
            if ($cnt != 1) {
                return resp(0, [
                    'err' => 'chat_session_id is not valid',
                    'hint' => 'make a new chat session',
                ]);
            }

            // check if the inviter is herself a member of the requested chat session
            $p = DBS::select(
                "select rec_id from tbl_chat_members where chat_session_id=? and chat_member_user_id=?",
                'ii',
                [
                    $chat_session_id,
                    $user_id,
                ]
            );
            $cnt = count($p['records']);
            if ($cnt < 1) {
                return resp(0, [
                    'err' => 'access denied',
                    'hint' => 'you are not a member of this chat session',
                    'chat_session_id' => $chat_session_id,
                    'user_id' => $user_id,
                ]);
            }

            // check if the given user is already a member of the chat session
            $p = DBS::select(
                "select rec_id from `tbl_chat_members` where `tbl_chat_members`.`chat_session_id` = ? and `tbl_chat_members`.`chat_member_user_id` = ? ",
                'ii',
                [
                    $chat_session_id,
                    $chat_member_user_id,
                ]
            );
            $cnt = count($p['records']);

            // TEST:
            // return resp(7, [
            //     $p,
            //     $chat_session_id,
            //     $chat_member_user_id,
            // ]);

            if ($cnt >= 1) {
                return resp(0, [
                    'err' => 'user is already a member of this chat session',
                ]);
            }

            // insert the new member
            $q = DBS::insert(
                "INSERT INTO `tbl_chat_members` (`rec_id`, `chat_session_id`, `chat_member_user_id`, `chat_member_inviter_id`, `chat_member_invitation_gdp`, `chat_member_blocked`) VALUES (NULL, ?, ?, ?, ?, '0')",
                'iiid',
                [
                    $chat_session_id,
                    $chat_member_user_id,
                    $user_id,
                    $gdp,
                ]
            );

            if ($q['affected_rows_count'] == 0) {
                return resp(0, 'adding new member to the requested chat session failed');
            }

            return resp(1, [
                'msg' => 'adding new member to the requested chat session succeeded',
                // 'inf' => $q,
            ]);
        }
        //
         catch (\Throwable $th) {
            return resp(0, 'unable to add new member to the requested chat session');
        }
    }

    public static function leaveChatSession()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $chat_session_id = par('chat_session_id');
        if ($chat_session_id == '') {
            return resp(0, 'missing chat_session_id');
        }

        if (!Chat::chatSessionExists($chat_session_id)) {
            return resp(0, 'invalid chat_session_id');
        }

        if (!Chat::isMemberOfChatSession($user_id, $chat_session_id)) {
            return resp(0, 'you are not a member of requested chat_session');
        }

        try {
            $r = DBS::delete(
                "delete from tbl_chat_members where chat_session_id=? and chat_member_user_id=?",
                'ii',
                [
                    $chat_session_id,
                    $user_id,
                ]
            );
            if ($r['affected_rows_count'] == 0) {
                resp(0, 'chat session leave process failed');
            }
            resp(1, 'chat session leave process succeeded');
        }
        //
         catch (\Throwable $th) {
            resp(0, 'unable to finish chat session leave process');
        }
    }

    public static function pushMessage()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $chat_session_id = par('chat_session_id');
        if ($chat_session_id == '') {
            return resp(0, 'missing chat_session_id');
        }

        // check if the user is a member of requested chat_session
        try {
            $r = DBS::select(
                "select count(rec_id) from tbl_chat_members where chat_session_id=? and chat_member_user_id=?",
                'ii',
                [
                    $chat_session_id,
                    $user_id,
                ]
            );
            $cnt = count($r['records']);
            if ($cnt == 0) {
                return resp(0, [
                    'err' => 'you are not a member of requested chat_session',
                    'hint' => 'ask a member to add you to the requested chat session',
                ]);
            }
        } //

         catch (\Throwable $th) {
            return resp(0, [
                'err' => 'unable to check if the user belongs to the requested chat session',
                'hint' => 'check sql statement',
            ]);

        }

        $message_text = par('message_text');
        if ($message_text == '') {
            return resp(0, 'missing message_text');
        }

        $c = new Calendar();
        $gdp = $c->get_server_gdp_time();

        try {
            // check if the requested chat session exists
            $r = DBS::select(
                "select count(chat_session_id) from tbl_chat_sessions where chat_session_id=?",
                'i',
                [
                    $chat_session_id,
                ]
            );
            $cnt = count($r['records']);
            if ($cnt != 1) {
                return resp(0, [
                    'err' => 'chat_session_id is not valid',
                    'hint' => 'make a new chat session',
                ]);
            }

            // insert record to tbl_chat_messages
            $message_sender_id = $user_id;
            $r = DBS::insert(
                "INSERT INTO `tbl_chat_messages` (`message_id`, `chat_session_id`, `message_sender_id`, `message_gdp`, `message_text`, `message_signals`) VALUES (NULL, ?, ?, ?, ?, '')",
                'iids',
                [
                    $chat_session_id,
                    $message_sender_id,
                    $gdp,
                    $message_text,
                ]
            );

            if ($r['affected_rows_count'] == 0) {
                return resp(0, [
                    'err' => 'push message failed',
                    'info' => $r,
                ]);
            }

            return resp(1, 'push message succeeded');
        } //
         catch (\Throwable $th) {
            return resp(0, 'unable  to finish push message process');
        }
    }

    public static function pullMessage()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $chat_session_id = par('chat_session_id');
        if ($chat_session_id == '') {
            return resp(0, [
                'err' => 'missing chat_session_id',
            ]);
        }

        $last_message_id = par('last_message_id');
        if ($last_message_id == '') {
            $last_message_id = 0;
        }

        try {
            $r = DBS::select(
                "select * from tbl_chat_messages where message_id>? and chat_session_id=?",
                'ii',
                [
                    $last_message_id,
                    $chat_session_id,
                ]
            );
            return resp(1, $r['records']);
        }
        //
         catch (\Throwable $th) {
            return resp(0, 'unable to fetch records from database');
        }
    }

    private static function isMemberOfChatSession($user_id, $chat_session_id)
    {
        $p = DBS::select(
            "select rec_id from tbl_chat_members where chat_session_id=? and chat_member_user_id=?",
            'ii',
            [
                $chat_session_id,
                $user_id,
            ]
        );
        $cnt = count($p['records']);
        if ($cnt == 0) {
            return false;
        } else {
            return true;
        }

    }

    private static function chatSessionExists($chat_session_id)
    {
        try {
            $r = DBS::select(
                "select chat_session_id from tbl_chat_sessions where chat_session_id=?",
                'i',
                [
                    $chat_session_id,
                ]
            );
            $cnt = count($r['records']);
            if ($cnt == 0) {
                return false;
            } else {
                return true;
            }

        } //

         catch (\Throwable $th) {
            return false;
        }
    }
}
