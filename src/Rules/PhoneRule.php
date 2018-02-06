<?php
namespace Zodream\Validate\Rules;


class PhoneRule extends AbstractRegexRule {

    protected function getPregFormat() {
        return '^(0|86|17951)?(13[0-9]|15[012356789]|17[013678]|18[0-9]|19[89]|14[57])[0-9]{8}$';
    }
}