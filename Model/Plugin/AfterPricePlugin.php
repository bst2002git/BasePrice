<?php

namespace Magenerds\BasePrice\Model\Plugin;

class AfterPricePlugin
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magenerds\BasePrice\Helper\Data
     */
    protected $_helper;

    /**
     * @var string
     */
    protected $_configurablePricesJson;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

		/**
     * @var \Magento\Catalog\Model\Product
     */
		protected $_product;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magenerds\BasePrice\Helper\Data $helper
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
		public function __construct(
			\Magento\Backend\Block\Template\Context $context,
		      \Magenerds\BasePrice\Helper\Data $helper,
		      \Magento\Framework\Registry $registry

		){
		      $this->_scopeConfig = $context->getScopeConfig();
		      $this->_helper = $helper;
		      $this->_registry = $registry;

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
					$product=$this->_product;
		      return $product;
		}

    public function aroundGetProductPrice(
    \Magento\Catalog\Block\Product\AbstractProduct $subject,
    \Closure $proceed,
    \Magento\Catalog\Model\Product $product)
    {
					$this->_product=$product;
					$this->getProduct();
					$this->getBasePrice();

					$returnHtml = $proceed($product);
					try{
						$customBlockHtml = $subject->getLayout()
							->createBlock('Magenerds\BasePrice\Block\AfterPrice','baseprice_afterprice_'.$product->getId(),
								[ 'data' =>
										['getBasePrice' => $this->getBasePrice()]
								])
							->setTemplate('Magenerds_BasePrice::afterprice_plugin.phtml')
							->toHtml();
						return $returnHtml.$customBlockHtml;

					} catch (\Exception $ex) {
            // if an error occurs, just render the default since it is preallocated
            return $renderHtml;
        }

    }
}