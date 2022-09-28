<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\SampleData\Installer;

use Amasty\GiftCard\Api\CodePoolRepositoryInterface;
use Amasty\GiftCard\Model\Code\CodeGeneratorManagement;
use Magento\Framework\Math\Random;
use Magento\Framework\Setup\SampleData\InstallerInterface;

class DefaultCodePool implements InstallerInterface
{
    public const SAMPLE_CODES_QTY = 1000;

    /**
     * @var CodePoolRepositoryInterface
     */
    private $codePoolRepository;

    /**
     * @var CodeGeneratorManagement
     */
    private $codeGeneratorManagement;

    /**
     * @var Random
     */
    private $random;

    public function __construct(
        CodePoolRepositoryInterface $codePoolRepository,
        CodeGeneratorManagement $codeGeneratorManagement,
        Random $random
    ) {
        $this->codePoolRepository = $codePoolRepository;
        $this->codeGeneratorManagement = $codeGeneratorManagement;
        $this->random = $random;
    }

    public function install()
    {
        $this->generateDefaultCodePool();
    }

    private function generateDefaultCodePool()
    {
        $randomTemplate = $this->random->getRandomString(3, "ABCDEFGHJKMNPRSTUVWXYZ")
            . '_' . $this->random->getRandomString(3, '23456789') . '_{L}{L}{D}{D}';

        $model = $this->codePoolRepository->getEmptyCodePoolModel();
        $model->setTitle('Sample Code Set')
            ->setTemplate($randomTemplate);

        try {
            $this->codePoolRepository->save($model);

            if (!$model->getCodePoolId()) {
                return;
            }
            $this->codeGeneratorManagement->generateCodesForCodePool(
                $model->getCodePoolId(),
                self::SAMPLE_CODES_QTY
            );
        } catch (\Exception $e) {
            return;
        }
    }
}
