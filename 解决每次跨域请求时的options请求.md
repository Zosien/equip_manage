调试过程中，发现几乎每次请求前都会有一个OPTION请求，这个请求并没有相应数据，第二个请求才是我们需要的请求，由于项目需要跨域，使用CORS，因此在每次**复杂请求**前都会有一个option请求。

option请求是一种预检请求，在跨域且当前请求为复杂请求时，会先向服务器发出预检请求，根据服务器返回的内容浏览器判断服务器是否允许该请求访问。

什么是复杂请求呢？与复杂请求对应的是简单请求，简单请求需满足以下条件：

1. 请求是GET/POST/ HEAD
2. 不能人为设置除Accept、Accept-Language、Content-Language、Content-Type外的其他header
3. Content-Type 的值只能是text/plain、multipart/form-data、application/x-www-form-urlencoded三者之一。

不满足以上条件的都是复杂请求。由于我在header里面设置了token字段，所以我的请求都是复杂请求，这样就造成了每次请求时都会有一个option请求这样一个麻烦，那如何解决这个麻烦呢？

考虑发送预检请求的条件，处理的方法也有几种

1. 改用其他方式解决跨域问题。
2. 将请求改为简单请求。
3. 服务端设置Access-Control-Max-Age

前两种都可以彻底消灭掉预检请求，最后一种是在服务端设置header里面的一个项，该项的值用来指定本次预检请求的有效期，单位为秒，在这个时间里，客户端不用频繁发送option请求来请示服务端。