<?php

class m1403278555_files_upload extends \Core\Db\Migration
{
    public function up()
    {
        @mkdir(CORE_UPLOAD_DIR.'/actions');
        @mkdir(CORE_UPLOAD_DIR.'/news');
    }
}
