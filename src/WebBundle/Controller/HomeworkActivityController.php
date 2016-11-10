<?php

namespace WebBundle\Controller;

use Topxia\Common\ArrayToolkit;
use Topxia\Service\Common\ServiceKernel;
use Biz\Activity\Service\ActivityService;
use Symfony\Component\HttpFoundation\Request;

class HomeworkActivityController extends BaseController implements ActivityActionInterface
{
    public function showAction(Request $request, $id, $courseId)
    {
        $activity = $this->getActivityService()->getActivity($id);

        return $this->forward('WebBundle:Homework:startDo', array(
            'homeworkId' => $activity['mediaId']
        ));
    }

    public function editAction(Request $request, $id, $courseId)
    {
        $activity = $this->getActivityService()->getActivity($id);

        $homeworkActivity = $this->getTestpaperService()->getTestpaper($activity['mediaId']);

        $activity = array_merge($activity, $homeworkActivity);

        $questionItems = $this->getTestpaperService()->searchItems(
            array('testId' => $activity['mediaId']),
            array('id' => 'DESC'),
            0, PHP_INT_MAX
        );

        $questions = $this->getQuestionService()->findQuestionsByIds(ArrayToolkit::column($questionItems, 'questionId'));

        return $this->render('WebBundle:HomeworkActivity:modal.html.twig', array(
            'activity'      => $activity,
            'courseId'      => $activity['fromCourseId'],
            'questionItems' => $questionItems,
            'questions'     => $questions
        ));
    }

    public function createAction(Request $request, $courseId)
    {
        return $this->render('WebBundle:HomeworkActivity:modal.html.twig', array(
            'courseId' => $courseId
        ));
    }

    protected function findCourseTestpapers($courseId)
    {
        $conditions = array(
            'courseId' => $courseId,
            'status'   => 'open'
        );

        $testpapers = $this->getTestpaperService()->searchTestpapers(
            $conditions,
            array('createdTime' => 'DESC'),
            0,
            PHP_INT_MAX
        );

        return $testpapers;
    }

    /**
     * @return ActivityService
     */
    protected function getActivityService()
    {
        return $this->createService('Activity:ActivityService');
    }

    protected function getTestpaperService()
    {
        return $this->createService('Testpaper:TestpaperService');
    }

    protected function getQuestionService()
    {
        return $this->getServiceKernel()->createService('Question.QuestionService');
    }

    protected function getServiceKernel()
    {
        return ServiceKernel::instance();
    }
}
