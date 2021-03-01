<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-event
 * @version   1.2.36
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Event\Ui\Event\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\Event\Api\Repository\EventRepositoryInterface;

class EventPlain implements OptionSourceInterface
{
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * EventPlain constructor.
     * @param EventRepositoryInterface $eventRepository
     */
    public function __construct(
        EventRepositoryInterface $eventRepository
    ) {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param null|array $options
     * @return array
     */
    public function toOptionArray($options = null)
    {
        $result = [];

        foreach ($this->eventRepository->getEvents() as $instance) {
            foreach ($instance->getEvents() as $identifier => $label) {
                $exploded = array_map('trim', explode('/', $label));

                if (count($exploded) == 2) {
                    $result[] = [
                        'label' => (string)$label,
                        'value' => $identifier,
                    ];
                } else {
                    $result[] = [
                        'label' => (string)$label,
                        'value' => $identifier,
                    ];
                }
            }
        }

        return array_values($result);
    }
}
