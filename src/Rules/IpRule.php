<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;

use Exception;

class IpRule extends AbstractRule {

    public  $ipOptions;

    public $networkRange;

    public function __construct(mixed $ipOptions = null) {
        if (is_int($ipOptions)) {
            $this->ipOptions = $ipOptions;

            return;
        }

        $this->networkRange = $this->parseRange($ipOptions);
    }

    protected function parseRange(mixed $input) {
        if (null === $input || '*' == $input || '*.*.*.*' == $input
            || '0.0.0.0-255.255.255.255' == $input) {
            return;
        }

        $range = ['min' => null, 'max' => null, 'mask' => null];

        if (false !== mb_strpos($input, '-')) {
            list($range['min'], $range['max']) = explode('-', $input);
        } elseif (false !== mb_strpos($input, '*')) {
            $this->parseRangeUsingWildcards($input, $range);
        } elseif (false !== mb_strpos($input, '/')) {
            $this->parseRangeUsingCidr($input, $range);
        } else {
            throw new Exception(
                __('Invalid network range')
            );
        }

        if (!$this->verifyAddress($range['min'])) {
            throw new Exception(
                __('Invalid network range')
            );
        }

        if (isset($range['max']) && !$this->verifyAddress($range['max'])) {
            throw new Exception(
                __('Invalid network range')
            );
        }

        return $range;
    }

    protected function fillAddress(&$input, string $char = '*') {
        while (mb_substr_count($input, '.') < 3) {
            $input .= '.'.$char;
        }
    }

    protected function parseRangeUsingWildcards($input, &$range) {
        $this->fillAddress($input);

        $range['min'] = strtr($input, '*', '0');
        $range['max'] = str_replace('*', '255', $input);
    }

    protected function parseRangeUsingCidr($input, &$range) {
        $input = explode('/', $input);
        $this->fillAddress($input[0], '0');

        $range['min'] = $input[0];
        $isAddressMask = false !== mb_strpos($input[1], '.');

        if ($isAddressMask && $this->verifyAddress($input[1])) {
            $range['mask'] = sprintf('%032b', ip2long($input[1]));

            return;
        }

        if ($isAddressMask || $input[1] < 8 || $input[1] > 30) {
            throw new Exception(
                __('Invalid network mask')
            );
        }

        $range['mask'] = sprintf('%032b', ip2long(long2ip(~(2 ** (32 - $input[1]) - 1))));
    }

    public function validate(mixed $input): bool {
        return $this->verifyAddress($input) && $this->verifyNetwork($input);
    }

    protected function verifyAddress(mixed $address): bool {
        return (bool) filter_var(
            $address,
            FILTER_VALIDATE_IP,
            [
                'flags' => $this->ipOptions,
            ]
        );
    }

    protected function verifyNetwork(mixed $input): bool {
        if (null === $this->networkRange) {
            return true;
        }

        if (isset($this->networkRange['mask'])) {
            return $this->belongsToSubnet($input);
        }

        $input = sprintf('%u', ip2long($input));

        return bccomp($input, sprintf('%u', ip2long($this->networkRange['min']))) >= 0
            && bccomp($input, sprintf('%u', ip2long($this->networkRange['max']))) <= 0;
    }

    protected function belongsToSubnet(mixed $input): bool {
        $range = $this->networkRange;
        $min = sprintf('%032b', ip2long($range['min']));
        $input = sprintf('%032b', ip2long($input));

        return ($input & $range['mask']) === ($min & $range['mask']);
    }
}