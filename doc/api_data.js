define({ "api": [
  {
    "type": "delete",
    "url": "/v1/article/delete",
    "title": "删除文章",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "id",
            "description": "<p>文章id</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\"id\":1}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n     \"state\":1,\n     \"msg\":\"success\",\n     \"data\":\"\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "DeleteV1ArticleDelete"
  },
  {
    "type": "delete",
    "url": "/v1/articleComment/comment",
    "title": "评论文章",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "article_id",
            "description": "<p>被评论的文章ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "content",
            "description": "<p>评论内容</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"article_id\":1,\n     \"content\":\"挣了5毛钱\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的响应：",
          "content": "{\n    \"state\": 1,\n    \"msg\": \"success\",\n    \"data\": {\n        \"user_name\": \"自干五\",\n        \"content\": \"挣了5毛钱\",\n        \"create_time\": \"07-25 18:57\",\n    }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/ArticleComment.php",
    "groupTitle": "Article",
    "name": "DeleteV1ArticlecommentComment"
  },
  {
    "type": "delete",
    "url": "/v1/articleComment/replyComment",
    "title": "回复评论",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "parent_id",
            "description": "<p>被回复的评论ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "content",
            "description": "<p>回复内容</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"parent_id\":1,\n     \"content\":\"挣了5美分\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的响应：",
          "content": "{\n    \"state\": 1,\n    \"msg\": \"success\",\n    \"data\": {\n        \"user_name\": \"白左\",\n        \"content\": \"挣了5美分\",\n        \"create_time\": \"07-25 18:57\",\n        \"parent_user_name\": \"自干五\"\n    }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/ArticleComment.php",
    "groupTitle": "Article",
    "name": "DeleteV1ArticlecommentReplycomment"
  },
  {
    "type": "get",
    "url": "/v1/article/classify",
    "title": "获取文章分类",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": true,
            "field": "parent_id",
            "description": "<p>分类的父ID (当前不传或只传0)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "略",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n     \"data\": [\n         {\n             \"id\": 1,\n             \"classify_name\": \"秀家\",\n             \"classify_img\": \"/home.png\"\n         },\n         {\n             \"id\": 2,\n             \"classify_name\": \"招聘\",\n             \"classify_img\": \"/home.png\"\n         },\n         {\n             \"id\": 3,\n             \"classify_name\": \"其他\",\n             \"classify_img\": \"/home.png\"\n         }\n     ]",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "GetV1ArticleClassify"
  },
  {
    "type": "get",
    "url": "/v1/article/collectMe",
    "title": "我的粉丝",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": "<p>页码</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": "<p>条目数</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "略",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n  \"state\": 1,\n  \"msg\": \"success\",\n  \"data\": {\n  \"total\": \"2\",                   //总粉丝数\n  \"list\":\n      [\n          {\n              \"id\": 1,\n              \"user_name\": \"MT\",\n              \"avatar\": \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\"\n          },\n          {\n              \"id\": 17,\n              \"user_name\": \"jinkela\",\n              \"avatar\":\n              \"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132\",\n          }\n      ]\n  }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "GetV1ArticleCollectme"
  },
  {
    "type": "get",
    "url": "/v1/article/details",
    "title": "根据id 获取文章详情",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "id",
            "description": "<p>文章id</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "略",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的响应：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": {\n \"id\": 1,                                //文章id\n \"user_id\": 1,                           //作者id\n \"user_name\": \"Trump\",                   //作者昵称\n \"avatar\": \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\",   //头像\n \"create_time\": \"2018-07-15 03:29:15\",    //发布时间\n \"music\":\n \"http://zhangmenshiting.qianqian.com/data2/music/dcd350d9c095d40d276914eece786513/594668014/594668014.mp3?xcode=40e5a4864e417ada180b9e6dd2675aac\",\n \"classify_name\": \"秀家\",                 //分类名\n \"pageview\": 5,                          //阅读数\n \"great_total\": 5,                       //点赞数\n \"comment_total\": 5,                     //评论数\n \"is_self\": false,                       //是否为当前用户自己发布的文章！\n \"content\":                              //文章图文内容\n     [\n         {\n             \"img\": \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\",\n             \"text\": \"我就是我\",\n             \"sort\": 1\n         },\n         {\n             \"img\": \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\",\n             \"text\": \"wewewewe\",\n             \"sort\": 1\n         }\n     ],\n \"comments\": [                           //文章的评论\n         {\n             \"id\": 22,                   //评论id\n             \"user_id\": 16,              //评论人id\n             \"user_name\": \"test2\",       //评论人昵称\n             \"avatar\":\n             \"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132\",\n             \"content\": \"挣了5毛钱22\",\n             \"great_total\": 1,\n             \"create_time\": 1532515666,\n         \"child\": [                      //评论的回复\n             {\n                 \"id\": 23,\n                 \"user_id\": 17,\n                 \"user_name\": \"user2\",\n                 \"respondent_user_name\": \"\",     //所回复的评论的发布人昵称   （首条回复，值为空）\n                 \"reply_content\": \"评论的回复\"    //回复内容\n             },\n             {\n                 \"id\": 24,\n                 \"user_id\": 16,\n                 \"user_name\": \"test2\",\n                 \"respondent_user_name\": \"Jack\",   //所回复的评论的发布人昵称\n                 \"reply_content\": \"回复的回复\"      //回复内容\n             }\n         ]\n     },\n     {\n         \"user_id\": 16,\n         \"user_name\": \"test2\",\n         \"avatar\":\n         \"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132\",\n         \"content\": \"挣了5毛钱\",\n         \"great_total\": 0,\n         \"create_time\": 1532515780,\n         \"id\": 25,\n         \"child\": []                               //此评论无回复\n         }\n     ]\n }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "GetV1ArticleDetails"
  },
  {
    "type": "get",
    "url": "/v1/article/getArticleList",
    "title": "获取文章列表统一接口",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "type",
            "description": "<p>列表类型  【homePage、self、byUid、classify】</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": true,
            "field": "order",
            "description": "<p>排序 默认0 ; 0 最新， 1 人气， 2 最近， 3 回复</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "keyword",
            "description": "<p>搜索关键字</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"type\":\"homePage\",\n     \"order\":0,\n     \"keyword\":\"我是标题\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的响应：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": {\n \"id\": 1,                                //文章id\n \"user_id\": 1,                           //作者id\n \"user_name\": \"Trump\",                   //作者昵称\n \"avatar\": \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\",   //头像\n \"create_time\": \"2018-07-15 03:29:15\",    //发布时间\n \"music\":\n \"http://zhangmenshiting.qianqian.com/data2/music/dcd350d9c095d40d276914eece786513/594668014/594668014.mp3?xcode=40e5a4864e417ada180b9e6dd2675aac\",\n \"classify_name\": \"秀家\",                 //分类名\n \"pageview\": 5,                          //阅读数\n \"great_total\": 5,                       //点赞数\n \"comment_total\": 5,                     //评论数\n \"is_self\": false,                       //是否为当前用户自己发布的文章！\n \"content\":                              //文章图文内容\n     [\n         {\n             \"img\": \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\",\n             \"text\": \"我就是我\",\n             \"sort\": 1\n         },\n         {\n             \"img\": \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\",\n             \"text\": \"wewewewe\",\n             \"sort\": 1\n         }\n     ],\n \"comments\": [                           //文章的评论\n         {\n             \"id\": 22,                   //评论id\n             \"user_id\": 16,              //评论人id\n             \"user_name\": \"test2\",       //评论人昵称\n             \"avatar\":\n             \"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132\",\n             \"content\": \"挣了5毛钱22\",\n             \"great_total\": 1,\n             \"create_time\": 1532515666,\n         \"child\": [                      //评论的回复\n             {\n                 \"id\": 23,\n                 \"user_id\": 17,\n                 \"user_name\": \"user2\",\n                 \"respondent_user_name\": \"\",     //所回复的评论的发布人昵称   （首条回复，值为空）\n                 \"reply_content\": \"评论的回复\"    //回复内容\n             },\n             {\n                 \"id\": 24,\n                 \"user_id\": 16,\n                 \"user_name\": \"test2\",\n                 \"respondent_user_name\": \"Jack\",   //所回复的评论的发布人昵称\n                 \"reply_content\": \"回复的回复\"      //回复内容\n             }\n         ]\n     },\n     {\n         \"user_id\": 16,\n         \"user_name\": \"test2\",\n         \"avatar\":\n         \"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132\",\n         \"content\": \"挣了5毛钱\",\n         \"great_total\": 0,\n         \"create_time\": 1532515780,\n         \"id\": 25,\n         \"child\": []                               //此评论无回复\n         }\n     ]\n }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "GetV1ArticleGetarticlelist"
  },
  {
    "type": "get",
    "url": "/v1/article/listByClassify",
    "title": "根据分类获取文章",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "classify_id",
            "description": "<p>分类id</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": true,
            "field": "order",
            "description": "<p>排序 默认0 ; 0 最新， 1 人气， 2 最近， 3 回复</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": "<p>页码</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": "<p>每页条目数</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\"classify_id\":1,\"page\":1,\"row\":10}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的响应：",
          "content": "{\n    // 同  ownList 接口\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "GetV1ArticleListbyclassify"
  },
  {
    "type": "get",
    "url": "/v1/article/localList",
    "title": "附近和已关注用户的动态",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": true,
            "field": "page",
            "description": "<p>页码</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": true,
            "field": "row",
            "description": "<p>每页条目数</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": true,
            "field": "order",
            "description": "<p>排序 默认0 ; 0 最新， 1 人气， 2 最近， 3 回复</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"order\":0,\n     \"page\":1,\n     \"row\":10\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": [\n {\n     \"id\": 2,\n     \"user_name\": \"test2\",\n     \"avatar\":\n     \"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132\",\n     \"create_time\": 1531596561,\n     \"classify_name\": \"秀家\",\n     \"pageview\": 6,          //查看数\n     \"great_total\": 0,       //点赞数\n     \"comment_total\": 0,     //评论数\n     \"content\": {\n         \"text\": \"队长，别点火！我甩不脱！ssdsddd\",      //第一个文字内容块中的文字\n         \"img\": [           //最多显示三张，没有则为空\n                 \"/static/img/article/cb2c82738fbe9165e94cadc6aada77ae.jpeg\",\n                 \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\",\n                 \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\"\n             ]\n         }\n     }\n}\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "GetV1ArticleLocallist"
  },
  {
    "type": "get",
    "url": "/v1/article/moreComment",
    "title": "获取文章更多评论",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "article_id",
            "description": "<p>文章id</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": "<p>页码</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": "<p>每页条目数</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"article_id\":1\n     \"page\":2,      //文章详情已输出10条评论， page 参数值可从 2 开始\n     \"row\":10\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的响应：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": [                           //文章的评论\n         {\n             \"id\": 22,                   //评论id\n             \"user_id\": 16,              //评论人id\n             \"user_name\": \"test2\",       //评论人昵称\n             \"avatar\":\n             \"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132\",\n             \"content\": \"挣了5毛钱22\",\n             \"great_total\": 1,\n             \"create_time\": 1532515666,\n         \"child\": [                      //评论的回复\n             {\n                 \"id\": 23,\n                 \"user_id\": 17,\n                 \"user_name\": \"user2\",\n                 \"respondent_user_name\": \"\",     //所回复的评论的发布人昵称   （首条回复，值为空）\n                 \"reply_content\": \"评论的回复\"    //回复内容\n             },\n             {\n                 \"id\": 24,\n                 \"user_id\": 16,\n                 \"user_name\": \"test2\",\n                 \"respondent_user_name\": \"Jack\",   //所回复的评论的发布人昵称\n                 \"reply_content\": \"回复的回复\"      //回复内容\n             }\n         ]\n     },\n     {\n         \"user_id\": 16,\n         \"user_name\": \"test2\",\n         \"avatar\":\n         \"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132\",\n         \"content\": \"挣了5毛钱\",\n         \"great_total\": 0,\n         \"create_time\": 1532515780,\n         \"id\": 25,\n         \"child\": []                               //此评论无回复\n         }\n     ]\n }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "GetV1ArticleMorecomment"
  },
  {
    "type": "get",
    "url": "/v1/article/myCollect",
    "title": "我关注的用户",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": "<p>页码</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": "<p>条目数</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "略",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n  \"state\": 1,\n  \"msg\": \"success\",\n  \"data\": {\n  \"total\": \"2\",                   //总关注数\n  \"list\":\n      [\n          {\n              \"id\": 1,\n              \"user_name\": \"MT\",\n              \"avatar\": \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\",\n              \"is_together\": true    //是否互相关注\n          },\n          {\n              \"id\": 17,\n              \"user_name\": \"jinkela\",\n              \"avatar\":\n              \"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132\",\n             \"is_together\": false\n          }\n      ]\n  }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "GetV1ArticleMycollect"
  },
  {
    "type": "get",
    "url": "/v1/article/myCollect",
    "title": "我收藏的文章",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": "<p>页码</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": "<p>每页条目数</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\"page\":1,\"row\":10}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的响应：",
          "content": "{\n    // 同  ownList 接口\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "GetV1ArticleMycollect"
  },
  {
    "type": "get",
    "url": "/v1/article/ownList",
    "title": "获取自己的文章列表",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": "<p>页码</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": "<p>每页条目数</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "无",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的响应：",
          "content": "{\n    \"state\": 1,\n    \"msg\": \"success\",\n    \"data\": [\n        {\n            \"id\": 1,\n            \"user_name\": \"test2\",\n            \"avatar\":\n            \"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132\",\n            \"create_time\": \"2018-07-15 03:29:15\",\n            \"classify_name\": \"秀家\",\n            \"pageview\": 5,\n            \"great_total\": 5,\n            \"comment_total\": 5,\n            \"content\": {\n            \"text\": \"我就是我\",\n            \"img\": [\n                \"fc38c299804217dfaf0ab4d04fbf0093.gif\",\n                \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\",\n                \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\"\n            ]\n        }\n    ]\n }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "GetV1ArticleOwnlist"
  },
  {
    "type": "get",
    "url": "/v1/article/ownList",
    "title": "根据用户id获取文章列表",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "user_id",
            "description": "<p>用户id</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\"user_id\":16}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的响应：",
          "content": "{\n    \"state\": 1,\n    \"msg\": \"success\",\n    \"data\": [\n        {\n            \"id\": 1,\n            \"user_name\": \"test2\",\n            \"avatar\":\n            \"https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJmRLtDgppCh5HkNXFVRyXqE0q49GBkC3kpCZgIaE2b4o62jDX4KZ5CloNn5MkYWu3VQocibb9FHWw/132\",\n            \"create_time\": \"2018-07-15 03:29:15\",\n            \"classify_name\": \"秀家\",\n            \"pageview\": 5,\n            \"great_total\": 5,\n            \"comment_total\": 5,\n            \"content\": {\n            \"text\": \"我就是我\",\n            \"img\": [\n                \"fc38c299804217dfaf0ab4d04fbf0093.gif\",\n                \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\",\n                \"/static/img/article/f7bd2c070f0c8323e1463018ab5e2433.png\"\n            ]\n        }\n    ]\n }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "GetV1ArticleOwnlist"
  },
  {
    "type": "post",
    "url": "/v1/article/create",
    "title": "创建文章",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "music",
            "description": "<p>背景音乐</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "classify_id",
            "description": "<p>分类id</p>"
          },
          {
            "group": "Parameter",
            "type": "array",
            "optional": false,
            "field": "content",
            "description": "<p>内容集</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n\"classify_id\":1,\n\"music:\"url****\",    //通过音乐接口获得\n\"content\":[\n     {\n         \"sort\":1,\n         \"img\":\"/static/img/tmp/90817f4f9e8fc6aa3ab09aa15e408b45.jpeg\",\n         \"text\":\"队长，别点火！我甩不开\"\n     },\n     {\n         \"sort\":2,\n         \"img\":\"/static/img/tmp/90817f4f9e8fc6aa3ab09aa15e408b45.jpeg\",\n         \"text\":\"队长，别点火！我甩不开2\"\n     }\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n     \"state\":1,\n     \"msg\":\"success\",\n     \"data\":\"12\"     //所写入数据的id\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "PostV1ArticleCreate"
  },
  {
    "type": "put",
    "url": "/v1/article/share",
    "title": "文章分享数 + 1",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "id",
            "description": "<p>文章id</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\"id\":1}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "     \"state\":1,\n     \"msg\":\"success\",\n     \"data\":\"\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "PutV1ArticleShare"
  },
  {
    "type": "put",
    "url": "/v1/article/update",
    "title": "更新文章",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "id",
            "description": "<p>文章id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "music",
            "description": "<p>背景音乐</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "classify_id",
            "description": "<p>分类id</p>"
          },
          {
            "group": "Parameter",
            "type": "array",
            "optional": false,
            "field": "content",
            "description": "<p>内容集</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n\"id\":1,\n\"classify_id\":1,\n\"music\":\"url****\",    //通过音乐接口获得\n\"content\":[\n     {\n         \"id\":2,         //有id，为更新\n         \"sort\":1,\n         \"img\":\"/static/img/tmp/90817f4f9e8fc6aa3ab09aa15e408b45.jpeg\",\n         \"text\":\"队长，别点火！我甩不开\"\n     },\n     {\n         //无id,为新增\n         \"sort\":2,\n         \"img\":\"/static/img/tmp/90817f4f9e8fc6aa3ab09aa15e408b45.jpeg\",\n         \"text\":\"队长，别点火！我甩不开2\"\n     }\n     //存在数据库，但id不在提交的数据中的，为删除\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n     \"state\":1,\n     \"msg\":\"success\",\n     \"data\":\"12\"     //所写入数据的id\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Article.php",
    "groupTitle": "Article",
    "name": "PutV1ArticleUpdate"
  },
  {
    "type": "get",
    "url": "/v1/factory/FactoryList",
    "title": "获取厂家列表",
    "group": "Factory",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": "<p>页码 （当前只有1页）</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": "<p>条目数 （当前只有10条数据）</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "见接口地址",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "[\n     {\n         \"id\": 2,\n         \"factory_contact\": \"王先生\",\n         \"factory_phone\": \"13800000000\",\n         \"factory_wx\": \"https://timgsa.baidu.com/timg?image&quality=8\",\n         \"wx_code\":\n         \"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1531989310611&di=899df6ccda3f15e7875f30c017cc5cf1&imgtype=0&src=http%3A%2F%2Fwww.alrui.com%2Fuploads%2Fallimg%2F180601%2F162I64395_0.jpg\",\n         \"province\": \"江西\",\n         \"city\": \"赣州\",\n         \"district\": \"南康\",\n         \"town\": \"龙岭\",\n         \"address\": \"金龙大道\",\n         \"factory_name\": \"宜家家居\",\n         \"factory_address\": \"工业西区\",\n         \"category_id\": 2,\n         \"category_child_id\": \"\",\n         \"user_name\": \"王大锤\",\n         \"phone\": \"13800000000\",\n         \"license_code\":\n         \"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1531989310611&di=899df6ccda3f15e7875f30c017cc5cf1&imgtype=0&src=http%3A%2F%2Fwww.alrui.com%2Fuploads%2Fallimg%2F180601%2F162I64395_0.jpg\",\n         \"factory_img\":\n         \"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1531989310611&di=899df6ccda3f15e7875f30c017cc5cf1&imgtype=0&src=http%3A%2F%2Fwww.alrui.com%2Fuploads%2Fallimg%2F180601%2F162I64395_0.jpg\"\n     }\n]",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "错误返回值：",
          "content": "{\n     \"state\":0,\n     \"msg\":\"错误信息\",\n     \"data\":[]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Factory.php",
    "groupTitle": "Factory",
    "name": "GetV1FactoryFactorylist"
  },
  {
    "type": "get",
    "url": "/v1/factory/FactoryList",
    "title": "获取厂家相册",
    "group": "Factory",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "factoryId",
            "description": "<p>厂家ID</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": "<p>页码 （当前只有1页）</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": "<p>条目数 （当前只有10条数据）</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "见接口地址",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n     \"state\": 1,\n     \"msg\": \"success\",\n     \"data\": {\n     \"row\": 10,\n     \"song_list\": [\n         {\n             \"id\": \"1990049\",                   //音乐ID\n             \"name\": \"小步舞曲\",                //音乐名\n             \"author\": \"贝多芬\",                //艺术家名\n             \"picture\":\n             \"http://qukufile2.qianqian.com/data2/music/FC9FD728B566E6CB18F1025F05689832/253348925/253348925.jpg@s_1,w_90,h_90\"\n               //歌曲图像\n             },\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "错误返回值：",
          "content": "{\n     \"state\":0,\n     \"msg\":\"错误信息\",\n     \"data\":[]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Factory.php",
    "groupTitle": "Factory",
    "name": "GetV1FactoryFactorylist"
  },
  {
    "type": "post",
    "url": "/v1/GoodsRetailPrice/setGlobalRatio",
    "title": "设置商城商品全局 零售价计算比例",
    "group": "GoodsRetailPrice",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "float",
            "optional": false,
            "field": "ratio",
            "description": "<p>零售价计算比例</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\"ratio\":1.3}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/GoodsRetailPrice.php",
    "groupTitle": "GoodsRetailPrice",
    "name": "PostV1GoodsretailpriceSetglobalratio"
  },
  {
    "type": "post",
    "url": "/v1/GoodsRetailPrice/setGoodsAmount",
    "title": "设置商城商品零售价",
    "group": "GoodsRetailPrice",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "float",
            "optional": false,
            "field": "amount",
            "description": "<p>商品零售价</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n \"amount\":50000,      //零售价\n \"goods_id\":1          //商品id\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/GoodsRetailPrice.php",
    "groupTitle": "GoodsRetailPrice",
    "name": "PostV1GoodsretailpriceSetgoodsamount"
  },
  {
    "type": "get",
    "url": "/v1/music/getByCategory",
    "title": "根据分类获取音乐列表",
    "group": "Music",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "category_id",
            "description": "<p>分类id</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": ""
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "略",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n   \"state\": 1,\n   \"msg\": \"success\",\n   \"data\": [\n       {\n           \"id\": 1,\n           \"name\": \"あの日の川へ\",             //音乐名\n           \"author\": \"久石让\",\n           \"link\": \"http://zhangmen28ebc34.mp3\",   //音乐文件地址\n           \"img\": \"http://qukufile2.qianqian.com/data2/pic/763021c8b43/596773143.jpg@s_1,w_90,h_90\"  //缩略图\n           },\n       {\n           \"id\": 2,\n           \"name\": \"あの日の川へ\",\n           \"author\": \"久石让\",\n           \"link\": \"http://zhangmen28ebc34.mp3\",   //音乐文件地址\n           \"img\": \"http://qukufile2.qianqian.com/data2/pic/763021c8b43/596773143.jpg@s_1,w_90,h_90\"  //缩略图\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Music.php",
    "groupTitle": "Music",
    "name": "GetV1MusicGetbycategory"
  },
  {
    "type": "get",
    "url": "/v1/music/getCategoryList",
    "title": "获取音乐库音乐分类",
    "group": "Music",
    "parameter": {
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "见接口地址",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n    \"state\": 1,\n    \"msg\": \"success\",\n    \"data\": [\n        {\n            \"id\": 1,\n            \"category_name\": \"天籁之音\",        //分类名\n            \"quantity\": 2                       //音乐数量\n        },\n        {\n            \"id\": 2,\n            \"category_name\": \"青葱校园\",\n            \"quantity\": 2\n        },\n        {\n            \"id\": 3,\n            \"category_name\": \"生活正能量\",\n            \"quantity\": 3\n        }\n    ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Music.php",
    "groupTitle": "Music",
    "name": "GetV1MusicGetcategorylist"
  },
  {
    "type": "get",
    "url": "/v1/music/getLink/:id",
    "title": "获取音乐文件地址",
    "group": "Music",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "id",
            "description": "<p>音乐的ID</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "见接口地址",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n     \"state\": 1,\n     \"msg\": \"success\",\n     \"data\": {\n             \"name\": \"あの日の川へ\",    //音乐名\n             \"author\": \"久石让\",        //艺术家名\n             \"link\": \"http://zhangmenshiting.qianqian.com/data2/music/f8d718a910550e85d0a3f7053488c221/596779534/596779534.mp3?xcode=b559392c4cf83addb6f28ebc1a3b5868\",        //文件在线地址\n             \"picture\": \"http://qukufile2.qianqian.com/data2/pic/763021e4882c8b773dc9c748d94d38df/596773143/596773143.jpg@s_1,w_90,h_90\",        //音乐图像\n         }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "错误返回值：",
          "content": "{\n     \"state\":0,\n     \"msg\":\"错误信息\",\n     \"data\":[]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Music.php",
    "groupTitle": "Music",
    "name": "GetV1MusicGetlinkId"
  },
  {
    "type": "get",
    "url": "/v1/music/query",
    "title": "根据音乐名或艺术家名模糊查找音乐",
    "group": "Music",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "keyword",
            "description": "<p>音乐名或艺术家名</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": ""
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "略",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n   \"state\": 1,\n   \"msg\": \"success\",\n   \"data\": [\n       {\n           \"id\": 1,\n           \"name\": \"あの日の川へ\",             //音乐名\n           \"author\": \"久石让\",\n           \"link\": \"http://zhangmen28ebc34.mp3\",   //音乐文件地址\n           \"img\": \"http://qukufile2.qianqian.com/data2/pic/763021c8b43/596773143.jpg@s_1,w_90,h_90\"  //缩略图\n           },\n       {\n           \"id\": 2,\n           \"name\": \"あの日の川へ\",\n           \"author\": \"久石让\",\n           \"link\": \"http://zhangmen28ebc34.mp3\",   //音乐文件地址\n           \"img\": \"http://qukufile2.qianqian.com/data2/pic/763021c8b43/596773143.jpg@s_1,w_90,h_90\"  //缩略图\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Music.php",
    "groupTitle": "Music",
    "name": "GetV1MusicQuery"
  },
  {
    "type": "get",
    "url": "/v1/music/recommend/:page/:row",
    "title": "获取推荐音乐",
    "group": "Music",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": "<p>页码 （当前只有1页）</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": "<p>条目数 （当前只有10条数据）</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "见接口地址",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n     \"state\": 1,\n     \"msg\": \"success\",\n     \"data\": {\n     \"row\": 10,\n     \"song_list\": [\n         {\n             \"id\": \"1990049\",                   //音乐ID\n             \"name\": \"小步舞曲\",                //音乐名\n             \"author\": \"贝多芬\",                //艺术家名\n             \"picture\": \"http://qukufile2.qianqian.com/data2/music/FC9FD728B566E6CB18F1025F05689832/253348925/253348925.jpg@s_1,w_90,h_90\"   //歌曲图像\n             },\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "错误返回值：",
          "content": "{\n     \"state\":0,\n     \"msg\":\"错误信息\",\n     \"data\":[]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Music.php",
    "groupTitle": "Music",
    "name": "GetV1MusicRecommendPageRow"
  },
  {
    "type": "get",
    "url": "/v1/music/search/:query",
    "title": "查找音乐",
    "group": "Music",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "query",
            "description": "<p>搜索条件</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "见接口地址",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n     \"state\": 1,\n     \"msg\": \"success\",\n     \"data\": {\n     \"row\": 10,\n     \"song\": [\n         {\n             \"id\": \"596779222\",        //音乐ID\n             \"name\": \"あの日の川へ\",    //音乐名\n             \"author\": \"久石让\",        //艺术家名\n         },\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "错误返回值：",
          "content": "{\n     \"state\":0,\n     \"msg\":\"错误信息\",\n     \"data\":[]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Music.php",
    "groupTitle": "Music",
    "name": "GetV1MusicSearchQuery"
  },
  {
    "type": "get",
    "url": "/v1/relate/blackList",
    "title": "获取当前用户的黑名单",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "category",
            "description": "<p>空或不传 则默认获取全部，goods - 厂家产品 ；shop 商家 ； factory 厂家</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": ""
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"category\":\"goods\",\n     \"page\":1,\n     \"row\":10\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"factory\": {                            //当前用户为商家时存在\n     \"name\": \"厂家\",\n     \"list\": []      //category = factory 时有值\n },\n \"shop\": {                                //当前用户为厂家时存在\n     \"name\": \"经销商\",\n     \"list\": []      //category = shop 时有值\n },\n \"goods\": {                              //当前用户为商家时存在\n     \"name\": \"商品\",\n     \"list\": []\n },\n \"default\": [                            //category 为空或未传 时有值\n     {\n         \"id\": 8,\n         \"factory_name\": \"双虎家居\",\n         \"factory_img\": \"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1531989310611&di=899df6ccda3f15e7875f30c017cc5cf1&imgtype=0&src=http%3A%2F%2Fwww.alrui.com%2Fuploads%2Fallimg%2F180601%2F162I64395_0.jpg\",\n         \"state\": 1,             // 禁用状态， 为 0 时， 提示该 厂家/商家/商品 已 冻结/下架\n         \"deleted\": \"\",          // 删除状态， 为 0 时则未删除，  非 0 时 （时间戳）则提示  该 厂家/商家/商品 已 不存在/删除\n         \"create_time\": \"2018-08-21\"         //收藏时间\n     },\n     {\n         \"id\": 1,\n         \"goods_name\": \"铁王座\",\n         \"state\": 1,\n         \"deleted\": \"0\",\n         \"shop_img\": \"/static/img/tmp/20180816\\\\\\\\b8faa0c919ad80eddd6aafc6eb519149.png\",\n         \"create_time\": \"2018-07-17\"\n     },\n     {\n         \"id\": 7,\n         \"shop_name\": \"三有家具城\",\n         \"shop_img\": \"/static/img/tmp/20180805\\\\209325e33c678d22c08c9a5e6715a1a3.jpg\",\n         \"state\": 1,\n         \"deleted\": \"0\",\n         \"create_time\": \"2018-07-17\"\n     }\n ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "GetV1RelateBlacklist"
  },
  {
    "type": "get",
    "url": "/v1/relate/collectList",
    "title": "获取用户收藏列表",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "category",
            "description": "<p>空或不传 则默认获取全部，goods - 厂家产品 ；shop 商家 ； factory 厂家</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": ""
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"category\":\"goods\",\n     \"page\":1,\n     \"row\":10\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"factory\": {\n     \"name\": \"厂家\",\n     \"list\": []      //category = factory 时有值\n },\n \"shop\": {\n     \"name\": \"经销商\",\n     \"list\": []      //category = shop 时有值\n },\n \"goods\": {\n     \"name\": \"商品\",\n     \"list\": []      //category = goods 时有值\n },\n \"default\": [        //category 为空或未传 时有值\n     {\n         \"id\": 8,\n         \"factory_name\": \"双虎家居\",\n         \"factory_img\": \"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1531989310611&di=899df6ccda3f15e7875f30c017cc5cf1&imgtype=0&src=http%3A%2F%2Fwww.alrui.com%2Fuploads%2Fallimg%2F180601%2F162I64395_0.jpg\",\n         \"state\": 1,             // 禁用状态， 为 0 时， 提示该 厂家/商家/商品 已 冻结/下架\n         \"deleted\": \"\",          // 删除状态， 为 0 时则未删除，  非 0 时 （时间戳）则提示  该 厂家/商家/商品 已 不存在/删除\n         \"create_time\": \"2018-08-21\"         //收藏时间\n     },\n     {\n         \"id\": 1,\n         \"goods_name\": \"铁王座\",\n         \"state\": 1,\n         \"deleted\": \"0\",\n         \"shop_img\": \"/static/img/tmp/20180816\\\\\\\\b8faa0c919ad80eddd6aafc6eb519149.png\",\n         \"create_time\": \"2018-07-17\"\n     },\n     {\n         \"id\": 7,\n         \"shop_name\": \"三有家具城\",\n         \"shop_img\": \"/static/img/tmp/20180805\\\\209325e33c678d22c08c9a5e6715a1a3.jpg\",\n         \"state\": 1,\n         \"deleted\": \"0\",\n         \"create_time\": \"2018-07-17\"\n     }\n ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "GetV1RelateCollectlist"
  },
  {
    "type": "post",
    "url": "/v1/relate/articleCollect",
    "title": "用户收藏文章",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "article_id",
            "description": "<p>文章ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "type",
            "description": "<p>inc(收藏) 或 dec(取消收藏)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"article_id\":1,\n     \"type\":\"inc\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "PostV1RelateArticlecollect"
  },
  {
    "type": "post",
    "url": "/v1/relate/articleGreat",
    "title": "用户点赞文章",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "article_id",
            "description": "<p>文章ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "type",
            "description": "<p>inc(点赞) 或 dec(取消点赞)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"article_id\":1,\n     \"type\":\"inc\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "PostV1RelateArticlegreat"
  },
  {
    "type": "post",
    "url": "/v1/relate/commentGreat",
    "title": "用户点赞评论",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "comment_id",
            "description": "<p>评论ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "type",
            "description": "<p>inc(点赞) 或 dec(取消点赞)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"comment_id\":1,\n     \"type\":\"inc\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "PostV1RelateCommentgreat"
  },
  {
    "type": "post",
    "url": "/v1/relate/factoryBlacklist",
    "title": "厂家拉黑商家",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "shop_id",
            "description": "<p>评论ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "type",
            "description": "<p>inc(拉黑) 或 dec(取消拉黑)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"shop_id\":1,\n     \"type\":\"inc\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "PostV1RelateFactoryblacklist"
  },
  {
    "type": "post",
    "url": "/v1/relate/factoryBlacklist",
    "title": "商家 拉黑 厂家",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "factory_id",
            "description": "<p>评论ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "type",
            "description": "<p>inc(拉黑) 或 dec(取消拉黑)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"factory_id\":1,\n     \"type\":\"inc\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "PostV1RelateFactoryblacklist"
  },
  {
    "type": "post",
    "url": "/v1/relate/factoryBlacklist",
    "title": "商家 拉黑 商城商品",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "goods_id",
            "description": "<p>评论ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "type",
            "description": "<p>inc(拉黑) 或 dec(取消拉黑)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"goods_id\":1,\n     \"type\":\"inc\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "PostV1RelateFactoryblacklist"
  },
  {
    "type": "post",
    "url": "/v1/relate/factoryCollect",
    "title": "用户收藏厂家",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "factory_id",
            "description": "<p>文章ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "type",
            "description": "<p>inc(收藏) 或 dec(取消收藏)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"factory_id\":1,\n     \"type\":\"inc\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "PostV1RelateFactorycollect"
  },
  {
    "type": "post",
    "url": "/v1/relate/goodsCollect",
    "title": "用户收藏商城商品",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "goods_id",
            "description": "<p>商品ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "type",
            "description": "<p>inc(关注) 或 dec(取消关注)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"goods_id\":1,\n     \"type\":\"inc\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "PostV1RelateGoodscollect"
  },
  {
    "type": "post",
    "url": "/v1/relate/shopCollect",
    "title": "用户收藏商家",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "shop_id",
            "description": "<p>文章ID</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "type",
            "description": "<p>inc(收藏) 或 dec(取消收藏)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"shop_id\":1,\n     \"type\":\"inc\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "PostV1RelateShopcollect"
  },
  {
    "type": "post",
    "url": "/v1/relate/shopCollect",
    "title": "用户关注用户",
    "group": "Relate",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "user_id",
            "description": "<p>被关注的用户id</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "type",
            "description": "<p>inc(关注) 或 dec(取消关注)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"user_id\":1,\n     \"type\":\"inc\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Relate.php",
    "groupTitle": "Relate",
    "name": "PostV1RelateShopcollect"
  },
  {
    "type": "get",
    "url": "/v1/sms/checkAuthCode/:phoneNumber/:authCode",
    "title": "校验短信验证码",
    "group": "SMS",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "phoneNumber",
            "description": "<p>手机号</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "authCode",
            "description": "<p>验证码</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "见接口地址",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n     \"state\":1,\n     \"msg\":\"验证通过\",\n     \"data\":[]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "错误返回值：",
          "content": "{\n     \"state\":0,\n     \"msg\":\"错误信息\",\n     \"data\":[]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Sms.php",
    "groupTitle": "SMS",
    "name": "GetV1SmsCheckauthcodePhonenumberAuthcode"
  },
  {
    "type": "get",
    "url": "/v1/sms/getAuthCode/:phoneNumber",
    "title": "发送短信验证码",
    "group": "SMS",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "phoneNumber",
            "description": "<p>手机号</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "见接口地址",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n     \"state\":1,\n     \"msg\":\"短信发送成功\",\n     \"data\":{\n         \"auth_code\":\"308299\"    //验证码，接收后可前端验证用户输入，也可通过请求验证接口校验\n     }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "错误返回值：",
          "content": "{\n     \"state\":0,\n     \"msg\":\"错误信息\",\n     \"data\":[]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Sms.php",
    "groupTitle": "SMS",
    "name": "GetV1SmsGetauthcodePhonenumber"
  },
  {
    "type": "get",
    "url": "/v1/store/homeGoodsList",
    "title": "获取商城首页商品列表",
    "group": "Store",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "shop_id",
            "description": "<p>当前商家id (商城入口处的商家)</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": true,
            "field": "page",
            "description": "<p>页码</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": true,
            "field": "row",
            "description": "<p>每页条目数</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "略",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n    \"state\": 1,\n    \"msg\": \"success\",\n    \"data\": [\n        {\n            \"goods_name\": \"铁王座\",\n            \"goods_no\": \"Y0123\",     //商城商品编号\n            \"popularity\": \"340\",     //人气值\n            \"img\": \"/static/img/tmp/20180816\\\\\\\\b8faa0c919ad80eddd6aafc6eb519149_thumb.png\",    //缩略图\n            \"price\": \"5000.00\",      //出厂价， 当前用户为此商城商家时 返回\n            \"model_no\": \"SH-0012\",   //厂家型号， 当前用户为此商城商家时 返回\n            \"retail_price\": \"8200\"    //零售价\n        },\n        {\n            \"goods_name\": \"帝王之床\",\n            \"goods_no\": \"C6542\",\n            \"popularity\": \"10\",\n            \"img\": \"/static/img/tmp/20180816\\\\\\\\b8faa0c919ad80eddd6aafc6eb519149_thumb.png\",\n            \"price\": \"6800.00\",         //出厂价， 当前用户为此商城商家时 返回\n            \"model_no\": \"SH-0013\",      //厂家型号， 当前用户为此商城商家时 返回\n            \"retail_price\": 8840\n        }\n    ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "错误返回值：",
          "content": "{\n     \"state\":0,\n     \"msg\":\"错误信息\",\n     \"data\":[]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/StoreGoods.php",
    "groupTitle": "Store",
    "name": "GetV1StoreHomegoodslist"
  },
  {
    "type": "get",
    "url": "/v1/getToken",
    "title": "获取userToken",
    "group": "Token",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "code",
            "description": "<p>微信用户的code</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"code\":\"*********\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n    \"state\": 1,\n    \"msg\": \"success\",\n    \"data\": {\n    \"token\": \"421ba5cb275fa6ee871d8288cffdbd17\",\n    \"user_info\": {\n            \"id\": 16,\n            \"user_name\": \"test\",\n            \"group_id\": 0,\n            \"avatar\": \"\",\n            \"gender\": 0,\n            \"phone\": \"1817074852\",\n            \"wx_account\": \"eeeFtyrty\",\n            \"type\": 3,\n            \"state\": 0,\n            \"create_time\": \"2018-06-15 04:15:23\"\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "错误返回值：",
          "content": "{\n     \"state\":0,\n     \"msg\":\"错误信息\",\n     \"data\":[]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Token.php",
    "groupTitle": "Token",
    "name": "GetV1Gettoken"
  },
  {
    "type": "get",
    "url": "/v1/userProposed/proposedList",
    "title": "用户的推荐列表",
    "group": "UserProposed",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "page",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "row",
            "description": ""
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "略",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n   \"state\": 1,\n   \"msg\": \"success\",\n   \"data\": [\n       {\n           \"type\": 1,                    //类型    1 厂家   2 经销商\n           \"group_id\": 9,                    // id\n           \"create_time\": \"2018-08-22\",    //推荐注册时间\n           \"group_name\": \"双虎家居\",\n           \"proposed_money\": 300           //提成\n       },\n       {\n           \"type\": 2,\n           \"group_id\": 14,\n           \"create_time\": \"2018-08-21\",\n           \"group_name\": \"潘峰家具城\",\n           \"proposed_money\": 500\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/UserProposed.php",
    "groupTitle": "UserProposed",
    "name": "GetV1UserproposedProposedlist"
  },
  {
    "type": "post",
    "url": "/v1/userProposed/proposed",
    "title": "保存推荐关系 (厂/商家用户在 某用户的推荐地址上申请注册)",
    "group": "UserProposed",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "number",
            "optional": true,
            "field": "user_id",
            "description": "<p>推荐人ID</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "略",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的数据：",
          "content": "{\n \"state\": 1,\n \"msg\": \"success\",\n \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/UserProposed.php",
    "groupTitle": "UserProposed",
    "name": "PostV1UserproposedProposed"
  },
  {
    "type": "post",
    "url": "/v1/image/temporary",
    "title": "临时存储图片 (base64格式)",
    "group": "image",
    "parameter": {
      "examples": [
        {
          "title": "请求参数格式：",
          "content": "{\n     \"img\":\"data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDA********\"\n}",
          "type": "string"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "成功时的返回：",
          "content": "{\n     \"data\": \"/static/img/tmp/f70cc04e10d49e0dfc8ea49029d38593.jpeg\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/v1/Image.php",
    "groupTitle": "image",
    "name": "PostV1ImageTemporary"
  }
] });
