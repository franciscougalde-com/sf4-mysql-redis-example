<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Helper\ErrorHelper;
use App\Service\RedisCacheService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/products", name="products_")
 */
class ApiController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(name="all")
     * @param Request           $request
     * @param RedisCacheService $redisCache
     *
     * @return JsonResponse
     */
    public function getProducts(Request $request, RedisCacheService $redisCache): JsonResponse {
        $source = 'Redis';
        $products = $redisCache->getObject('products');
        if (empty($products)) {
            $source = 'Mysql';
            $em = $this->getDoctrine()->getManager();
            $products = $em->getRepository(Product::class)->findAll();
            $redisCache->setObject('products', $products, $redisCache::TTL_DAY);
        }

        return JsonResponse::create([
            'source' => $source,
            'data' => $products,
        ]);
    }

    /**
     * @Rest\Post(name="add")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addProduct(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(ProductType::class, new Product());
        $form->submit($data);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return JsonResponse::create(
                ErrorHelper::generateErrorOutputFromForm($form),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $product = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return JsonResponse::create($product, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Rest\Post(path="/cache-invalidate", name="invalidate")
     * @param Request           $request
     * @param RedisCacheService $redisCache
     *
     * @return JsonResponse
     */
    public function invalidateCache(Request $request, RedisCacheService $redisCache): JsonResponse {
        $redisCache->invalidate('products');

        return JsonResponse::create(["Cache invalidated!"]);
    }
}
