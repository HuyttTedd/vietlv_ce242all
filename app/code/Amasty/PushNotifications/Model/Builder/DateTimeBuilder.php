<?php

namespace Amasty\PushNotifications\Model\Builder;

use Amasty\PushNotifications\Model\ConfigProvider;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Status;
use Magento\Framework\Stdlib\DateTime as DateTimeFormat;
use Magento\Framework\Stdlib\DateTime\DateTime;

class DateTimeBuilder implements BuilderInterface
{
    const DATETIME_FORMAT_WITH_FULL_MONTH_NAME = "j F Y";
    const PASSED_CAMPAIGNS_UPPER_BOUND = 3;
    const SCHEDULED_CAMPAIGNS_UPPER_BOUND = 2;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var DateTimeFormat
     */
    private $dateTimeFormat;

    public function __construct(
        ConfigProvider $configProvider,
        DateTime $dateTime,
        DateTimeFormat $dateTimeFormat
    ) {
        $this->configProvider = $configProvider;
        $this->dateTime = $dateTime;
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * @inheritdoc
     */
    public function build(array $params)
    {
        return $params;
    }

    /**
     * @return int
     */
    public function getCurrentTimestamp()
    {
        return $this->dateTime->gmtTimestamp();
    }

    /**
     * @return null|string
     */
    public function getCurrentFormatedTime()
    {
        return $this->dateTimeFormat->formatDate($this->getCurrentTimestamp());
    }

    /**
     * @param \Amasty\PushNotifications\Api\Data\CampaignInterface $campaign
     * @return string
     */
    public function getScheduledDateFromDifference(\Amasty\PushNotifications\Api\Data\CampaignInterface $campaign)
    {
        return $this->timeElapsedString(
            $campaign->getScheduled(),
            $campaign->getStatus() == Status::STATUS_SCHEDULED ? false : true
        );
    }

    /**
     * @param $datetime
     * @param bool $ago
     * @return string
     */
    private function timeElapsedString($datetime, $ago = true)
    {
        $resultString = '';
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        $moreThanThreeDays = (isset($string['d']) && (int)$string['d'] >= self::PASSED_CAMPAIGNS_UPPER_BOUND)
            || isset($string['m']) || isset($string['w']) || isset($string['y']);
        $hours = isset($string['h']) ? $string['h'] : '';
        $minutes = isset($string['i']) ? $string['i'] : '';
        $seconds = isset($string['s']) && !isset($string['h']) ? $string['s'] : '';

        if (!isset($string['d']) && !$moreThanThreeDays) {
            $time = $hours . ' ' .  $minutes;
            switch (true) {
                case $ago < $now:
                    $resultString = __('Today %1 ago', $hours . ' ' .  $minutes . ' ' . $seconds);
                    break;
                case $ago > $now:
                    $resultString = __('Today in %1', $hours . ' ' .  $minutes . ' ' . $seconds);
                    break;
                case $ago == $now:
                    break;
                    $resultString = __('Now');
            }
        } elseif (!$moreThanThreeDays && (int)$string['d'] < self::PASSED_CAMPAIGNS_UPPER_BOUND) {
            $dateText = $this->getTextForDateInFutureOrPast($now, $ago, $string);
            $resultString = $ago ? $dateText : __('Tomorrow %1', $hours . ' ' .  $minutes);
        } else {
            $resultString = $ago->format(self::DATETIME_FORMAT_WITH_FULL_MONTH_NAME);
        }

        return $resultString;
    }

    /**
     * @param $nowDate
     * @param $checkDatetime
     * @param $dateArray
     *
     * @return \Magento\Framework\Phrase
     */
    private function getTextForDateInFutureOrPast($nowDate, $checkDatetime, $dateArray)
    {
        return $checkDatetime < $nowDate ? __('%1 ago', $dateArray['d']) : __('in %1', $dateArray['d']);
    }
}
