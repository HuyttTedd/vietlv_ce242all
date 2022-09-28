<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Model\Image\OutputBuilders;

use Amasty\GiftCard\Model\Image\ImageElementConfigProvider;
use Amasty\GiftCard\Model\Image\OutputBuilders\OutputBuilderInterface;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\GiftCardAccount\Repository;
use Psr\Log\LoggerInterface;

class HtmlOutputBuilder implements OutputBuilderInterface
{
    /**
     * @var ImageElementConfigProvider
     */
    private $imageElementConfigProvider;

    /**
     * @var GiftCardAccountInterface
     */
    private $accountModel;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ImageElementConfigProvider $imageElementConfigProvider,
        Repository $accountRepository,
        LoggerInterface $logger,
        string $code
    ) {
        $this->imageElementConfigProvider = $imageElementConfigProvider;
        $this->accountModel = $accountRepository->getByCode($code);
        $this->logger = $logger;
    }

    public function build(array $imageElements): string
    {
        $result = '';

        foreach ($imageElements as $element) {
            if (!($elementConfig = $this->imageElementConfigProvider->get($element->getName()))) {
                continue;
            }
            try {
                $element->setWidth($element->getWidth() ?: $elementConfig->getDefaultElement()->getWidth())
                    ->setHeight($element->getHeight() ?: $elementConfig->getDefaultElement()->getHeight())
                    ->setValueDataSource($this->accountModel);
                $result .= $elementConfig->getProcessor()->generateHtml($element);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        return $result;
    }
}
