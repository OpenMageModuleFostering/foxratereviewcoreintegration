<?php


class Foxrate_Sdk_FoxrateRCI_RichSnippets
{
    const PRODUCT_SCOPE = 'ProductScope';

    const PRODUCT_PROP_NAME = 'ProductPropName';

    protected $config;

    protected $problem;


    public function __construct(Foxrate_Sdk_FoxrateRCI_RichSnippetConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param mixed $problem
     */
    public function setProblem($problem)
    {
        $this->config->saveRichSnippetProblem($problem);
    }

    /**
     * @return mixed
     */
    public function getProblem()
    {
        return $this->config->getRichSnippetProblem();
    }

    public function isProblem()
    {
        return $this->config->isRichSnippetProblem();
    }

    /**
     *
     *
     * Try to add rich snippet only if we don't have problems.
     * To clear setting, user must enable again RichSnippet again
     *
     * @param $content
     * @param $hookName
     *
     * @return mixed
     */
    public function addInTemplate($content, $hookName)
    {

        if ($this->isProblem()) {
            return $content;
        }

        $id = $this->config->getTemplateIdentifier($hookName);
        if (!$this->isInContent($id, $content)) {
            return $content;
        }

        try {
            $content = $this->replaceHookWihRichSnippet($content, $hookName);

        } catch (Foxrate_Sdk_FoxrateRCI_Exception_RichSnippetHookNotFound $e) {

            $this->setDisableRichSnippets($e->getMessage());
        }

        return $content;
    }

    /**
     * Check validity of template by searching for identifier.
     * If identifier is not set, skip checkin.
     *
     * @param $id
     * @param $content
     *
     * @return bool
     */
    private function isInContent($id, $content){
        if (empty($id)) {
            return true;
        }

        return strpos($content, $id) !== false;
    }

    private function replaceHookWihRichSnippet($content, $hookName)
    {
        $search = $this->config->getHook($hookName);
        $replace = $this->config->getHookTarget($hookName);

        if ($this->hookExists($search, $content)) {
            return $this->addRichSnippet($search, $replace, $content);
        }

        throw new Foxrate_Sdk_FoxrateRCI_Exception_RichSnippetHookNotFound(
            sprintf(
                'Adding rich snippets failed and rich snippets are disabled.'
                . "\nElement not found in template: %s. "
                . "\nEnsure element exists and try enable rich snippets "
                . "\nby saving settings with enabled rich snippets. ",
                htmlentities($search)
            )

        ) ;
    }

    private function hookExists($search, $content)
    {
        return strpos($content, $search) !== false;
    }

    private function addRichSnippet($search, $replace, $content)
    {
        return str_replace($search, $replace, $content);
    }

    private function setDisableRichSnippets($getMessage)
    {
        $this->setProblem($getMessage);
        $this->config->disableRichSnippets();
    }
}
