<?php

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author louis <louis@systemli.org>
 */
class UserCRUDController extends CRUDController
{
    /**
     * @param ProxyQueryInterface $selectedModelQuery
     * @param Request             $request
     *
     * @return RedirectResponse
     *
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function batchActionRemoveVouchers(ProxyQueryInterface $selectedModelQuery, Request $request)
    {
        $this->admin->checkAccess('edit');

        $users = $selectedModelQuery->execute();

        $this->get('App\Remover\VoucherRemover')->removeUnredeemedVouchersByUsers($users);

        $this->addFlash(
            'sonata_flash_success',
            'flash_batch_remove_vouchers_success'
        );

        return new RedirectResponse(
            $this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters()))
        );
    }
}