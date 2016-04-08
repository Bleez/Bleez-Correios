<?php
namespace Bleez\Correios\Block\System\Config\Form\Field;

/**
 * Config Renderer Services
 */
class ServicesNames extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @var string
     */
    protected $_template = 'Bleez_Correios::system/config/form/field/services.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }


    protected function _construct()
    {
        $this->addColumn('id', ['label' => __('ID'), 'size' => '6', 'disabled' => true]);
        $this->addColumn('service', ['label' => __('Serviço'), 'disabled' => true]);
        $this->addColumn('name', ['label' => __('Nome')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add \Exception');
        parent::_construct();
    }

    /**
     * @param string $name
     * @param array $params
     * @return void
     */
    public function addColumn($name, $params)
    {
        $this->_columns[$name] = [
            'label' => $this->_getParam($params, 'label', 'Column'),
            'size' => $this->_getParam($params, 'size', false),
            'style' => $this->_getParam($params, 'style'),
            'class' => $this->_getParam($params, 'class'),
            'disabled' => $this->_getParam($params, 'disabled', false),
            'renderer' => false,
        ];
        if (!empty($params['renderer']) && $params['renderer'] instanceof \Magento\Framework\View\Element\AbstractBlock) {
            $this->_columns[$name]['renderer'] = $params['renderer'];
        }
    }

    /**
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new \Exception('Wrong column name specified.');
        }
        $column = $this->_columns[$columnName];
        $inputName = $this->_getCellInputElementName($columnName);

        if ($column['renderer']) {
            return $column['renderer']->setInputName(
                $inputName
            )->setInputId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setColumnName(
                $columnName
            )->setColumn(
                $column
            )->toHtml();
        }

        return '<input type="text" id="' . $this->_getCellInputElementId(
            '<%- _id %>',
            $columnName
        ) .
        '"' .
        ' name="' .
        $inputName .
        '" value="<%- ' .
        $columnName .
        ' %>" ' .
        ($column['size'] ? 'size="' .
            $column['size'] .
            '"' : '') .
        ' class="' .
        (isset(
            $column['class']
        ) ? $column['class'] : 'input-text') . '"' . (isset(
            $column['style']
        ) ? ' style="' . $column['style'] . '"' : '') . (
            $column['disabled']
         ? ' disabled' : '') . '/>';
    }
}
