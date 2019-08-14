<?php

class Foxrate_Sdk_ServiceBundle_Account
{
    private $accountList;

    public function __construct(
        Foxrate_Sdk_ListBundle_Account $userList
    ) {
        $this->accountList = $userList;
    }

    public function getAccountByAccountId($accountId)
    {
        $userList = $this->getNewAccountList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_Account();
        $filterBuilder->setId($accountId);

        $userList->addFilters($filterBuilder->getFilters());
        return $userList->getFirst($accountId);
    }

    public function getNewAccountList()
    {
        $this->accountList->clear();
        return $this->accountList;
    }

}