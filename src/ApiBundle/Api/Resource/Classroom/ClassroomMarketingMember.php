<?php

namespace ApiBundle\Api\Resource\Classroom;

use ApiBundle\Api\Annotation\Access;
use ApiBundle\Api\Exception\ErrorCode;
use ApiBundle\Api\ApiRequest;
use ApiBundle\Api\Resource\AbstractResource;
use Biz\Classroom\Service\ClassroomService;
use Biz\Marketing\Service\MarketingService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClassroomMarketingMember extends AbstractResource
{
    /**
     * @param ApiRequest $request
     * @param $classroomId
     *
     * @return array
     * @Access(roles="ROLE_ADMIN,ROLE_SUPER_ADMIN")
     */
    public function add(ApiRequest $request, $classroomId)
    {
        $classroom = $this->getClassroomService()->getClassroom($classroomId);
        if (empty($classroom)) {
            throw new NotFoundHttpException('班级不存在', null, ErrorCode::RESOURCE_NOT_FOUND);
        }
        $biz = $this->getBiz();
        $logger = $biz['logger'];
        $logger->debug('微营销通知处理订单');
        $postData = $request->request->all();

        try {
            return $this->getMarketingService()->addUserToClassroom($postData);
        } catch (\Exception $e) {
            $logger->error($e);

            return array('code' => 'error', 'msg' => 'ES处理微营销订单失败,'.$e->getTraceAsString());
        }
    }

    /**
     * @return MarketingService
     */
    protected function getMarketingService()
    {
        return $this->service('Marketing:MarketingService');
    }

    /**
     * @return ClassroomService
     */
    protected function getClassroomService()
    {
        return $this->service('Classroom:ClassroomService');
    }
}
