<?php
 
namespace RedAlmond\Content\Setup\Patch\Data;
 
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Api\Data\PageInterfaceFactory;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class AddCmsPage
 * @package RedAlmond\Content\Setup\Patch\Data
 */
class AddCmsPage implements DataPatchInterface
{
    const IDENTIFIER = 'buttons';
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
 
    /**
     * @var PageInterfaceFactory
     */
    private $pageFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var PageRepositoryInterface
     */
    private $cmsPageRepository;
 
    /**
     * AddCmsPage constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param PageInterfaceFactory $pageFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PageRepositoryInterface $cmsPageRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        PageInterfaceFactory $pageFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PageRepositoryInterface $cmsPageRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->pageFactory = $pageFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cmsPageRepository = $cmsPageRepository;
    }
 
    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $content = '<style>.grid {  display: grid; grid-template-columns: 1fr 1fr 1fr; grid-gap: 40px; }</style>
<h1>BUTTONS</h1>
<div class="grid">
<div class="col"><button class="button primary tocart" type="button"><span><span>Load More</span></span></button></div>
<div class="col"><button class="button btn-continue" title="Continue Shopping" type="button"><span><span>Continue Shopping</span></span></button></div>
<div class="col"><button class="action primary checkout" type="button">Checkout</button></div>
</div>';

        $this->moduleDataSetup->startSetup();
        try {
            $this->cleanCmsPages();
            $page = $this->pageFactory->create();
            $page->setIdentifier(static::IDENTIFIER);
            $page->setContent($content);
            $page->setTitle('Buttons');
            $page->setIsActive(1);
            $page->setPageLayout('1column');
            $this->cmsPageRepository->save($page);
        } catch(\Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Delete existing CMS page(s) if exists
     */
    public function cleanCmsPages()
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(PageInterface::IDENTIFIER, static::IDENTIFIER)
            ->create();
        $cmsPageCollection = $this->cmsPageRepository
            ->getList($searchCriteria)
            ->getItems();

        foreach ($cmsPageCollection as $cmsPage) {
            $this->cmsPageRepository->delete($cmsPage);
        }
    }
 
    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
 
    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }
 
    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
