<?php
	use app\models\Settings;
	use app\models\ProductUpdates;
	use yii\helpers\Url;
	use app\assets\AppAsset;
	AppAsset::register($this);
	
	$version = "0";
	$settings_info = Settings::getCustomversion();
	//find()->select('fieldvalue')->where("field = 'custom_version'")->one();
	if(isset($settings_info->fieldvalue) && $settings_info->fieldvalue!=""){
		$version = $settings_info->fieldvalue;
	}else{
		$version = ProductUpdates::getCustomVersion();
		//find()->orderBy("id desc")->one()->version;;
	}
?>
<footer class="footer" role="contentinfo">
  <div class="container">
    <div class="footer-main">
	  <div id="term-condition"></div>
	  <p class="text-center">&copy; <?php echo date('Y')?> Inovitech, LLC All Rights Reserved. <a href="javascript:void(0);" title="Terms" id="terms_popup">Terms</a>&nbsp; | &nbsp; Version <span id="isatask_version"> <?php echo $version; ?> </span></p>
    </div>
  </div>
	<div class="modal white_content" id="Terms" style="display: none;">
		<div  aria-labelledby="dialog-title" aria-describedby="dialog-description" role="dialog" class="white_content2">
			<div id="errorContent" class="contact-container">
				<h2 id="dialog-title">Terms:-</h2>
				<br></br><span id="dialog-description">THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.</span></div>
			<div>
				<a title="Click here to close Dialouge box." class='btn btn-primary' onclick="document.getElementById('Terms').style.display = 'none';" href="javascript:void(0)" tabindex="0">
					<em class="fa fa-close btn_close" alt="Click here to close Dialouge box."></em>
				</a>
			</div>
		</div>
	</div>
</footer>
<script>
	$(document).ready(function () {
		$('body').on('click', '#terms_popup', function () {
            $("#Terms").show();
            $("#Terms").focus();
        });
    });
</script>

