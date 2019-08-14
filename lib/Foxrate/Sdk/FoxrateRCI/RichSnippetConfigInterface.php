<?php


interface Foxrate_Sdk_FoxrateRCI_RichSnippetConfigInterface {

    public function getRichSnippetProblem();

    public function saveRichSnippetProblem($message);

    public function isRichSnippetProblem();

    public function getHook($hookName);

    public function getHookTarget($hookName);
} 