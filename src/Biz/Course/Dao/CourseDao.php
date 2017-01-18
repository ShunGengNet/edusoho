<?php

namespace Biz\Course\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface CourseDao extends GeneralDaoInterface
{
    public function findCoursesByCourseSetIdAndStatus($courseSetId, $status);

    public function getDefaultCourseByCourseSetId($courseSetId);

    public function findCoursesByIds($ids);

    public function findPriceIntervalByCourseSetIds($courseSetIds);
}
