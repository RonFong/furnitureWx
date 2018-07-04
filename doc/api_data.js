define({ "api": [
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
  }
] });