<?php
// FT-09 Safe output: a text typed by a user is always shown as plain text, never run as HTML.
function e(?string $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
