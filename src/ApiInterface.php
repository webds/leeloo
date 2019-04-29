<?php

/*
 * LeeLoo REST API Interface
 * Roman WebDS Telegram WebDS_Net
 * Documentation
 * https://leelooai.atlassian.net/wiki/spaces/DOC/pages/465108998/Write+API#WriteAPI-%D0%94%D0%BE%D0%B1%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5%D0%B7%D0%B0%D0%BA%D0%B0%D0%B7%D0%B0
 *
 */

namespace Webds\Leeloo;

interface ApiInterface
{

    /**
     * Create new address book
     *
     * @param $bookName
     */
    public function orders(
        $paymentCreditsId,
        $email,
        $phone,
        $transactionDate,
        $offerId,
        $accountId,
        $isNotifyAccount
    );


}
