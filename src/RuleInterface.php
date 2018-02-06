<?php
namespace Zodream\Validate;


interface RuleInterface {

    public function assert($input);

    public function check($input);

    public function reportError($input, array $relatedExceptions = []);

    public function validate($input);
}