<?php

namespace App\Services;

use App\Post as EloquentPost;
use Appkr\Thrift\Post\Post as ThriftPost;
use Appkr\Thrift\Post\PostField;
use Appkr\Thrift\Post\PostServiceIf;
use Appkr\Thrift\Post\QueryFilter;

class PostService implements PostServiceIf
{
    /**
     * 포스트 목록을 응답합니다.
     *
     * @param \Appkr\Thrift\Post\QueryFilter $qf
     * @param int $offset
     * @param int $limit
     * @return \Appkr\Thrift\Post\Post[] PostCollection 엔티티
     *
     * @throws \Appkr\Thrift\Errors\UserException
     * @throws \Appkr\Thrift\Errors\SystemException
     */
    public function all(QueryFilter $qf, $offset, $limit)
    {
        $builder = new EloquentPost;

        if ($qf->keyword) {
            // 고급 DB를 사용한다면 Full text BOOLEAN MATCH 사용할 수 있음
            $builder->where('title', 'like', "%{$qf->keyword}%");
        }

        $posts = $builder->orderBy($qf->sortBy, $qf->sortDirection)
                         ->offset($offset)
                         ->limit($limit)
                         ->get();

        return $posts->map(function ($post) {
            return new ThriftPost($post->toArray());
        })->all();
    }

    /**
     * 특정 포스트의 상세 정보를 응답합니다.
     *
     * @param int $id
     * @return \Appkr\Thrift\Post\Post Post 엔티티
     *
     * @throws \Appkr\Thrift\Errors\UserException
     * @throws \Appkr\Thrift\Errors\SystemException
     */
    public function find($id)
    {
        $post = EloquentPost::findOrFail($id);

        return new ThriftPost($post->toArray());
    }

    /**
     * 새 포스트를 만듭니다.
     *
     * @param ThriftPost $thriftPost
     * @return ThriftPost Post 엔티티
     */
    public function store(ThriftPost $thriftPost)
    {
        $eloquentPost = EloquentPost::create([
            'title' => $thriftPost->title,
            'content' => $thriftPost->content,
        ]);

        return new ThriftPost($eloquentPost->toArray());
    }
}