<?php

namespace Bitty\Tests\Controller;

use Bitty\Container\ContainerAwareInterface;
use Bitty\Controller\AbstractController;
use Bitty\Http\Exception\InternalServerErrorException;
use Bitty\Router\UriGeneratorInterface;
use Bitty\View\ViewInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

class AbstractControllerTest extends TestCase
{
    /**
     * @var AbstractController
     */
    private $fixture = null;

    /**
     * @var ContainerInterface|MockObject
     */
    private $container = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->createMock(ContainerInterface::class);

        $this->fixture = $this->getMockForAbstractClass(AbstractController::class, [$this->container]);
    }

    public function testRedirectToRouteCallsContainer(): void
    {
        $uriGenerator = $this->createUriGenerator();

        $this->container->expects(self::once())
            ->method('get')
            ->with('uri.generator')
            ->willReturn($uriGenerator);

        $this->fixture->redirectToRoute(uniqid());
    }

    public function testRedirectToRouteCallsUriGenerator(): void
    {
        $name         = uniqid('name');
        $params       = [uniqid('param')];
        $uriGenerator = $this->createUriGenerator();

        $this->container->method('get')->willReturn($uriGenerator);

        $uriGenerator->expects(self::once())
            ->method('generate')
            ->with($name, $params);

        $this->fixture->redirectToRoute($name, $params);
    }

    public function testRedirectToRouteResponse(): void
    {
        $uri          = uniqid('uri');
        $uriGenerator = $this->createUriGenerator($uri);

        $this->container->method('get')->willReturn($uriGenerator);

        $actual = $this->fixture->redirectToRoute(uniqid());

        self::assertInstanceOf(ResponseInterface::class, $actual);
        self::assertEquals([$uri], $actual->getHeader('Location'));
        self::assertEquals(302, $actual->getStatusCode());
    }

    public function testGetCallsContainer(): void
    {
        $id = uniqid('service');

        $this->container->expects(self::once())
            ->method('get')
            ->with($id);

        $this->fixture->get($id);
    }

    public function testGetResponse(): void
    {
        $value = uniqid('value');

        $this->container->method('get')->willReturn($value);

        $actual = $this->fixture->get(uniqid());

        self::assertEquals($value, $actual);
    }

    public function testRenderCallsContainer(): void
    {
        $view = $this->createView();

        $this->container->expects(self::once())
            ->method('get')
            ->with('view')
            ->willReturn($view);

        $this->fixture->render(uniqid());
    }

    public function testRenderThrowsException(): void
    {
        $message = 'Container service "view" must be an instance of '.ViewInterface::class;
        $this->expectException(InternalServerErrorException::class);
        $this->expectExceptionMessage($message);

        $this->fixture->render(uniqid());
    }

    public function testRenderCallsView(): void
    {
        $template = uniqid('template');
        $data     = [uniqid('data')];
        $view     = $this->createView();

        $this->container->method('get')->willReturn($view);

        $view->expects(self::once())
            ->method('render')
            ->with($template, $data);

        $this->fixture->render($template, $data);
    }

    public function testRenderResponse(): void
    {
        $html = uniqid('html');
        $view = $this->createView($html);

        $this->container->method('get')->willReturn($view);

        $actual = $this->fixture->render(uniqid());

        self::assertInstanceOf(ResponseInterface::class, $actual);
        self::assertEquals($html, (string) $actual->getBody());
    }

    /**
     * Creates a URI generator.
     *
     * @param string|null $uri
     *
     * @return UriGeneratorInterface|MockObject
     */
    private function createUriGenerator(?string $uri = null): UriGeneratorInterface
    {
        $uriGenerator = $this->createMock(UriGeneratorInterface::class);
        $uriGenerator->method('generate')->willReturn($uri ?? uniqid());

        return $uriGenerator;
    }

    /**
     * Creates a view.
     *
     * @param string $html
     *
     * @return ViewInterface|MockObject
     */
    private function createView(string $html = ''): ViewInterface
    {
        $view = $this->createMock(ViewInterface::class);
        $view->method('render')->willReturn($html);

        return $view;
    }
}
