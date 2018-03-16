<%-- test.jsp --%>
 
<%@ page language="java"%>
 
<%!
 
int a= 100;
 
int b= 200;
 
%>
 
<%
 
int c= 0;
 
c=a+b;
 
%>
 
<html>
 
<head><title> JSP Test </title></head>
 
<body>
 
a= <%=a%><br>
 
b= <%=b%><br>
 
c= <%=c%>
 
</body>
 
</html>
