<?php
/**
 * Helper
 */
class modMaxPosterManufacturerHelper
{
    private $xml, $xpath;
    private $params, $app;
    private $search = array();

    /**
     * Construct
     *
     * @param  type  variable
     */
    public function __construct(DomDocument $xml, JRegistry $params)
    {
        $this->xml = $xml;
        $this->xpath = new DOMXpath($this->xml);

        $this->params = $params;
        $this->app = JFactory::getApplication();
    }


    /**
     * Make options from array
     *
     * @param  array   $options
     * @return string
     */
    private function tagOptions(array $options = array())
    {
        $ret = array();
        foreach ($options as $name => $value) {
            $ret[] = sprintf('%s="%s"', (string) $name, (string) $value);
        }

        return implode(' ', $ret);
    }


    /**
     * Make html tag
     *
     * @param  string  $name
     * @param  array   $options
     * @return string
     */
    private function tag($name, array $options = array())
    {
        $options = $this->tagOptions($options);
        return sprintf('<%s%s />', $name, $options ? ' ' . $options : '');
    }


    /**
     * Make html tag
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    private function contentTag($name, $value = false, array $options = array())
    {
        if (false === $value) {
            return $this->tag($name, $options);
        }
        $options = $this->tagOptions($options);

        return sprintf('<%1$s%3$s>%2$s</%1$s>', $name, (string) $value, $options ? ' ' . $options : '');
    }


    /**
     * Get com_maxposter menu identifier
     *
     * @return mixed
     */
    private function getLinkMenuId()
    {
        $maxComponent = JComponentHelper::getComponent('com_maxposter');
        $menu = $this->app->getMenu();
        $items = $menu->getItems('component_id', $maxComponent->id);
        return (!empty($items['0']) ? sprintf('&Itemid=%d', $items['0']->id) : '');
    }


    /**
     * Build internal route link for JRoute::_() method
     *
     * @return string
     */
    private function buildInternalLink()
    {
        return sprintf('index.php?option=com_maxposter&view=list%s', $this->getLinkMenuId());
    }


    /**
     * Search parameters
     *
     * @param  array  $search
     * @return void
     */
    public function setSearch(array $search = array())
    {
        $this->search = $search;
    }


    /**
     * Список марок и моделей
     *
     * @param  string  $type
     * @return string
     */
    public function getList($type = 'ul', $withModels = true)
    {
        $xmlMarks = $this->xpath->query('/response/marks/mark');
        $link = $this->buildInternalLink();
        $searchPrefix = $this->params->get('prefix', '');

        // marks
        $output = '';
        foreach ($xmlMarks as $mark) {
            $markHref = sprintf('%s&%ssearch[mark_id]=%d', $link, $searchPrefix, (int) $mark->getAttribute('mark_id'));
            // models
            $models = '';
            if ($withModels) {
                foreach ($this->xpath->query('./models/model', $mark) as $model) {
                    $modelHref = sprintf('%s&%ssearch[model_id]=%d', $markHref, $searchPrefix, (int) $model->getAttribute('model_id'));
                    $modelName = $this->xpath->query('./name', $model)->item(0);
                    $modelLink = $this->contentTag('a', $modelName->nodeValue, array('href' => JRoute::_($modelHref)));
                    $models .= $this->contentTag('li', $modelLink, array(
                        'class' => sprintf('mod-maxposter-manufacturers-model-%d', (int) $model->getAttribute('model_id'))
                                . (
                                    (!empty($this->search['model_id']) && ($this->search['model_id'] == (int) $model->getAttribute('model_id')))
                                    ? ' current' : ''
                                ),
                    ));
                }
                unset($modelHref, $modelLink);
                $models = $models ? $this->contentTag('ul', $models, array()) : '';
                //
            }
            $markName = $this->xpath->query('./name', $mark)->item(0);
            $markLink = $this->contentTag('a', $markName->nodeValue, array('href' => JRoute::_($markHref)));
            $output .= $this->contentTag('li', $markLink . $models, array(
                'class' => sprintf('mod-maxposter-manufacturers-mark-%d', (int) $mark->getAttribute('mark_id'))
                        .  (
                            (!empty($this->search['mark_id']) && ($this->search['mark_id'] == (int) $mark->getAttribute('mark_id')))
                            ? ' current' : ''
                        ),
            ));
        }
        unset($markHref, $markLink, $models);

        return $output ? $this->contentTag('ul', $output) : '';
    }

}
