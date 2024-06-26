<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace EBT\ExtensionBuilder\ViewHelpers;

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

class DomainObjectChecksViewHelper extends AbstractConditionViewHelper
{
    protected ExtensionBuilderConfigurationManager $configurationManager;

    public function injectExtensionBuilderConfigurationManager(
        ExtensionBuilderConfigurationManager $configurationManager
    ): void {
        $this->configurationManager = $configurationManager;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('domainObject', 'object', '', true);
        $this->registerArgument('renderCondition', 'string', '', true);
    }

    /**
     * Helper function to verify various conditions around possible mapping/inheritance configurations
     *
     * @return string
     */
    public function render()
    {
        /** @var DomainObject $domainObject */
        $domainObject = $this->arguments['domainObject'];
        $extensionPrefix = 'Tx_' . $domainObject->getExtension()->getExtensionName();

        // an external table should have a loadable TCA configuration and the column definitions
        // for external tables have to be declared in ext_tables.php
        $isMappedToExternalTable = false;

        // table name is only set, if the model is mapped to a table or if the domain object extends a class
        $tableName = $domainObject->getMapToTable();

        if ($tableName && !str_contains($tableName, strtolower($extensionPrefix) . '_domain_model_')) {
            $isMappedToExternalTable = true;
        }
        return match ($this->arguments['renderCondition']) {
            'isMappedToInternalTable' => !$isMappedToExternalTable,
            'isMappedToExternalTable' => $isMappedToExternalTable,
            'needsTypeField' => $this->needsTypeField($domainObject, $isMappedToExternalTable),
            default => '',
        };
    }

    /**
     * Do we have to create a type field in database and configuration?
     *
     * A type field is needed if either the domain objects extends another class
     * or if other domain objects of this extension extend it or if it is mapped
     * to an existing table
     *
     * @param DomainObject $domainObject
     * @param bool $isMappedToExternalTable
     */
    protected function needsTypeField(DomainObject $domainObject, bool $isMappedToExternalTable): bool
    {
        if ($isMappedToExternalTable || $domainObject->getChildObjects()) {
            $tableName = $domainObject->getDatabaseTableName();
            if (!isset($GLOBALS['TCA'][$tableName]['ctrl']['type'])
                || $GLOBALS['TCA'][$tableName]['ctrl']['type'] === 'tx_extbase_type'
            ) {
                /*
                 * if the type field is set but equals the default extbase record type field name it might
                 * have been defined by the current extension and thus has to be defined again when rewriting TCA definitions
                 * this might result in duplicate definition, but the type field definition is always wrapped in a condition
                 * "if (!isset($GLOBALS['TCA'][table][ctrl][type]){ ..."
                 *
                 * If we don't check the TCA at runtime it would result in a repetition of type field definitions
                 * in case an extension has multiple models extending other models of the same extension
                 */
                return true;
            }
        }
        return false;
    }
}
