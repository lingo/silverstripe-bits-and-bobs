<%-- Example use of Pluralizer --%>

<%-- Here we access a DataObjectSet returned by a controller function or page field --%>
<% if Items %>
	<span>$Items.Count $Items.Plural(result) were found.</span>
<% end_if %>
