<?php

namespace Data\Notifications;

use Database\Session;

class NotificationType
{
    public string $type = "";
    protected $from;
    protected $to;
    protected NotificationRef $ref;

    public function __construct()
    {
        $this->from = Session::UserData()['id'];
        $this->ref = new NotificationRef();
    }

    public function Send(): void
    {
        if (!isset($this->to)) return;
        Utils::SendNotification($this->from, $this->to, $this->ref, $this);
    }
}