{assign var="dynform" value=$simpleSearchForm}
		<!-- start library search -->
			<div class="container_elements">
				<h1>##Library Search##</h1>
                <form action="ui_handler.php" method="post" name="simplesearch" id="simplesearch"><input name="act" type="hidden" value="SEARCH.simpleSearch" />
					<div>
						<input size="20" maxlength="50" name="criterium" type="text" style="width: 184px;" />
						<input type="button" class="button_small" value="##Go##" />
					</div>
				</form>
			</div>
		<!-- end library search -->