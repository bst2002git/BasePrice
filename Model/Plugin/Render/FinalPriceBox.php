<?php
namespace Magenerds\BasePrice\Model\Plugin\Render;

use Magento\Catalog\Pricing\Price;
use Magento\Framework\Pricing\Render\PriceBox as BasePriceBox;
use Magento\Msrp\Pricing\Price\MsrpPrice;
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;

class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
		/**
     * @var SalableResolverInterface
     */
    private $salableResolver;

		/**
     * @var MinimalPriceCalculatorInterface
     */
    private $minimalPriceCalculator;

		/**
     * @var \Magenerds\BasePrice\Helper\Data
     */
    protected $_helper;

		/**
     *
     * @var \Magento\Catalog\Block\Product\AbstractProduct
     */
    protected $_subject;

		/**
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param array $data
     * @param SalableResolverInterface $salableResolver
     * @param MinimalPriceCalculatorInterface $minimalPriceCalculator
     */
    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
				\Magenerds\BasePrice\Helper\Data $helper,
				\Magento\Catalog\Block\Product\AbstractProduct $subject,
        array $data = [],
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minimalPriceCalculator = null
    ) {
        parent::__construct($context, $saleableItem, $price, $rendererPool, $data);
        $this->salableResolver = $salableResolver ?: ObjectManager::getInstance()->get(SalableResolverInterface::class);
        $this->minimalPriceCalculator = $minimalPriceCalculator
            ?: ObjectManager::getInstance()->get(MinimalPriceCalculatorInterface::class);
				 $this->_helper = $helper;
				 $this->_subject = $subject;
    }


		/**
     * Returns the base price information
     */
    public function getBasePrice()
    {
				return $this->_helper->getBasePriceText($this->getProduct());
    }

		/**
		* Retrieve current product
		*
		* @return \Magento\Catalog\Model\Product
		*/
		public function getProduct()
		{
		      return $this->getSaleableItem();
		}

		/**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (!$this->salableResolver->isSalable($this->getSaleableItem())) {
            return '';
        }

        $result = parent::_toHtml();
        //Renders MSRP in case it is enabled
        if ($this->isMsrpPriceApplicable()) {
            /** @var BasePriceBox $msrpBlock */
            $msrpBlock = $this->rendererPool->createPriceRender(
                MsrpPrice::PRICE_CODE,
                $this->getSaleableItem(),
                [
                    'real_price_html' => $result,
                    'zone' => $this->getZone(),
                ]
            );
            $result = $msrpBlock->toHtml();
        }

				try{
						$customBlockHtml = $this->_subject->getLayout()
							->createBlock('Magenerds\BasePrice\Block\AfterPrice','baseprice_afterprice_'.$this->getProduct()->getId(),
								[ 'data' =>
										['getBasePrice' => $this->getBasePrice()]
								])
							->setTemplate('Magenerds_BasePrice::afterpricebox_plugin.phtml')
							->toHtml();
						return $this->wrapResult($result.$customBlockHtml);

					} catch (\Exception $ex) {
            // if an error occurs, just render the default since it is preallocated
            return $this->wrapResult($result);
        }

    }

}