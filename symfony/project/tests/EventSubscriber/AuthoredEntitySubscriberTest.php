<?php

namespace App\Tests\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use App\EventSubscriber\AuthoredEntitySubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthoredEntitySubscriberTest extends TestCase
{
    public function testConfiguration()
    {
        $result = AuthoredEntitySubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::VIEW, $result);
        $this->assertEquals(
            ['getAuthenticatedUser', EventPriorities::PRE_WRITE],
            $result[KernelEvents::VIEW]
        );
    }

    /**
     * Test the call to setAuthor
     *
     * @dataProvider providerSetAuthorCall
     *
     * @param string $className
     * @param boolean $shouldCallSetAuthor
     * @param string $method
     * @return void
     */
    public function testSetAuthorCall(string $className, bool $shouldCallSetAuthor, string $method)
    {
        (new AuthoredEntitySubscriber($this->getTokenStorageMock()))->getAuthenticatedUser(
            $this->getEventMock(
                $method,
                $this->getEntityMock($className, $shouldCallSetAuthor)
            )
        );
    }

    public function testNoTokenPresent()
    {
        (new AuthoredEntitySubscriber($this->getTokenStorageMock(false)))->getAuthenticatedUser(
            $this->getEventMock(
                'POST',
                new class

        {}
            )
        );
    }

    /**
     * Data provider for testSetAuthorCall
     *
     * @return array
     */
    public function providerSetAuthorCall(): array
    {
        return [
            [BlogPost::class, true, 'POST'],
            [BlogPost::class, false, 'GET'],
            ['NonExisting', false, 'POST'],
            ['NonExisting', false, 'GET'],
            [Comment::class, true, 'POST'],
        ];
    }

    /**
     * Mocking TokenStorageInterface
     *
     * @param boolean $hasToken
     * @return MockObject|TokenStorageInterface
     */
    private function getTokenStorageMock(bool $hasToken = true): TokenStorageInterface
    {
        $tokenMock = $this->getMockBuilder(TokenInterface::class)->getMockForAbstractClass();
        $tokenMock->expects($hasToken ? $this->once() : $this->never())
            ->method('getUser')
            ->willReturn(new User);

        /** @var TokenStorageInterface $tokenStorageMock */
        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)->getMockForAbstractClass();

        $tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn($hasToken ? $tokenMock : null);

        return $tokenStorageMock;
    }

    /**
     * Mocking ViewEvent
     *
     * @param string $method
     * @param mixed $controllerResult
     * @return MockObject|ViewEvent
     */
    private function getEventMock(string $method, $controllerResult): ViewEvent
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->getMock();

        $requestMock->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        /** @var ViewEvent $eventMock */
        $eventMock = $this->getMockBuilder(ViewEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock->expects($this->once())
            ->method('getControllerResult')
            ->willReturn($controllerResult);

        $eventMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        return $eventMock;
    }

    /**
     * Mock an Entity
     *
     * @param mixed $className
     * @param boolean $shouldCallSetAuthor
     * @return MockObject
     */
    private function getEntityMock($className, bool $shouldCallSetAuthor): MockObject
    {
        $entityMock = $this->getMockBuilder($className)
            ->setMethods(['setAuthor'])
            ->getMock();

        $entityMock->expects($shouldCallSetAuthor ? $this->once() : $this->never())
            ->method('setAuthor');

        return $entityMock;
    }
}
