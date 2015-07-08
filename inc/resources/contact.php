<!--

manifold-impact-analytics
https://github.com/braunsg/manifold-impact-analytics

Open source code for Manifold, an automated impact analytics and visualization platform developed by
Steven Braun.

COPYRIGHT (C) 2015 Steven Braun

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  

A full copy of the license is included in LICENSE.md.

//////////////////////////////////////////////////////////////////////////////////////////
/////// About this file

Contact form allowing users to submit questions to the general Manifold e-mail alias

-->

<script>
$(document).ready(function() {
	$("#contact_submit_button").click(function() {
		var send_form = 1;
		var reqd_fields = ["contact_name","contact_email","contact_message"];
		for(var field in reqd_fields) {
			if($("#" + reqd_fields[field]).val() === "") {
				send_form = 0;
				alert("Please fill in all fields.");
				break;
			}
			if(reqd_fields[field] === "contact_email") {
				var check_email = $("#contact_email").val();
				var validate = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				if(!validate.test(check_email)) {
					alert("Please enter a valid e-mail address.");
					send_form = 0;
				} 
			}
		}
		if(send_form == 1) {
			if($("#message_response").length == 1) {
				$("#message_response").html("");
			}
		
			if($("#message_response").length == 0) {
				$("#contact_form_container").prepend($("<div  id='message_response'></div>"));
			}
		
			$("#message_response").html("<span style='text-align:center;width:100%;display:block;'><img src='inc/images/loading_circle.gif' style='margin-right:5px;'>Sending message...</span>");


			var contact_name = $("#contact_name").val();
			var contact_email = $("#contact_email").val();
			var contact_message = $("#contact_message").val();
			$.post("inc/resources/resources-scripts/send-message.php",{name: contact_name, email: contact_email, message: contact_message}, function(contact_response) {
				$("#message_response").html(contact_response);
			});
		}
	});
});

</script>

<div class="page_header">Other Questions</div>
<div class="faq_container">
	<div class="faq_content">
	Still have questions about <i>Manifold</i> or the data? Use the following form to submit your questions to the contact address. Before submitting your question, please make sure that your question has not already been answered in the <a href="resources.php?p=faq">Frequently Asked Questions</a>.
	<br><br>
	<b>Please note</b>: This form should only be used to submit questions about the data or system of Manifold. For questions on the Scholarship Metrics Initiative or Medical School requirements, please consult your department chair or the Office of the Dean.
	</div>
</div>
<div class="faq_container">
	<div class="faq_header">Contact form</div>
	<div class="faq_content">All fields are required.</div>
</div>
<div id="contact_form_container">
	<div class="contact_form_row">
		<div class="contact_form_fieldlabel">Name<span class="field_required">*</span></div>
		<div class="contact_form_field"><input maxlength="150" type="text" id="contact_name" class="textfield"></div>
	</div>
	<div class="contact_form_row">
		<div class="contact_form_fieldlabel">E-mail<span class="field_required">*</span></div>
		<div class="contact_form_field"><input maxlength="40" type="text" id="contact_email" class="textfield"></div>
	</div>
	<div class="contact_form_row">
		<div class="contact_form_fieldlabel">Question<span class="field_required">*</span></div>
		<div class="contact_form_field"><textarea wrap="soft" maxlength="2000" id="contact_message" class="textarea"></textarea></div>
	</div>
	<div class="contact_form_row">
		<div class="contact_submit"><input type="submit" value="Send Message" id="contact_submit_button"></div>

</div>
