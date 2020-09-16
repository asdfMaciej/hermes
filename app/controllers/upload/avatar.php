<?php
namespace Web\Controllers;
use \Model\User;
use mysql_xdevapi\Exception;

include_once ROOT_PATH . "/application/lib/bulletproof/bulletproof.php";
include_once ROOT_PATH . "/application/lib/bulletproof/utils/func.image-resize.php";
include_once ROOT_PATH . "/application/lib/bulletproof/utils/func.image-crop.php";

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
		$this->addActions(["upload" => "onUpload"]);
	}

	protected function content() {}

	protected function onUpload() {
		$image = new \Bulletproof\Image($_FILES);
		if ($image["image"]) {
			$image->setMime(['jpeg', 'jpg', 'png']);
			$image->setLocation(ROOT_PATH . "/uploads/img/avatars");
			if ($success = $image->upload()) {
			    $path_before = $image->getFullPath();
			    $path_after = $image->getLocation().'/'.$image->getName().'.jpg';
			    $width = $image->getWidth();
			    $height = $image->getHeight();

				\Bulletproof\Utils\resize(
                    $path_before,
					"jpg",
					$width,
                    $height,
                    500, 500, // width x height
					true// keep the ratio
				);

				rename($path_before, $path_after);

                list($w, $h) = getimagesize($path_after);

				\Bulletproof\Utils\crop(
				    $path_after,
                    "jpg",
                    $w,
                    $h,
                    min(500, min($w, $h)),
                    min(500, min($w, $h))
                );

				$user = User::getSingleItem($this->database, ["user_id" => $this->account->user_id]);
				$user->avatar = "uploads\\img\\avatars\\" . $image->getName().".jpg";
				$success = $user->save($this->database);
			} else {
			    throw new \Exception($image->getError());
            }
		}

		$this->account->loginUser($user);
		return $this->redirect("profile/".$this->account->user_id);
	}
}

new Page();
?>