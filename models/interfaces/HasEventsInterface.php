<?php

namespace app\models\interfaces;

use app\models\Event;

interface HasEventsInterface {
    public function getHistoryBody(Event $event): string;
}