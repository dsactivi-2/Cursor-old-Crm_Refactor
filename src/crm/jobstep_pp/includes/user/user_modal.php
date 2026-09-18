<!-- Modal -->
<div class="modal fade" id="userModal" aria-hidden="true" aria-labelledby="userModalLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
	<div class="modal-dialog modal-dialog-centered modal-sm">
		<div class="modal-content border-0">
			<div class="modal-header border-bottom-0 text-center">
				<h5 class="modal-title w-100" id="userModalLabel"><?php echo $txtArray["Korisnički račun"][$languageUser]; ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<main class= "justify-content-center">
					<ul class="nav nav-pills flex-column mb-auto">
						<!--<li>
							<a href="#" class="nav-link link-dark">
								<i class="fa fa-cog me-2" aria-hidden="true"></i>
								<?php /*echo $txtArray["Postavke"][$languageUser]; */?>
							</a>
						</li>-->
						<li>
							<div class="nav-link link-dark">
								<i class="fa fa-user-circle-o me-2" aria-hidden="true"></i>
								<?php echo getFirstAndLastNameUserR($userId); /*$txtArray["Profil"][$languageUser];*/ ?>
							</div>
						</li>
						<li>
							<a href="<?php getSiteUrl();?>do.php?page=logOut" class="nav-link link-dark"> 
								<i class="fa fa-sign-out me-2" aria-hidden="true"></i>
								<?php echo $txtArray["Odjavite se"][$languageUser]; ?>
							</a>
						</li>
					</ul>
				</main>
			</div>
		</div>
	</div>
</div>