<?php

function getNotificationStatus($title, $description)
{
    if (empty($title) || empty($description)) {
        return "Thiếu thông tin";
    }

    if (mb_strlen($description, 'UTF-8') >= 100) {
        return "Thông báo chi tiết";
    }

    return "Thông báo ngắn";
}
