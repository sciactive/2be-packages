<?php
/**
 * com_pdf_displays class.
 *
 * @package Components\pdf
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A display holder entity.
 *
 * Use this entity to provide the information com_pdf needs to print information
 * onto a PDF.
 *
 * @package Components\pdf
 */
class com_pdf_displays extends Entity {
	const etype = 'com_pdf_displays';
	protected $tags = array('com_pdf', 'displays');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->displays = array();
		$this->pdf_file = 'blank.pdf';
		$this->pdf_dl_filename = 'blank.pdf';
		$this->pdf_pages = 1;
		$this->pdf_title = 'blank';
		$this->pdf_author = $_->config->com_pdf->author;
		$this->pdf_creator = '2be';
		$this->pdf_subject = '';
		$this->pdf_keywords = '';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				if (isset($this->pdf_title))
					return $this->pdf_title;
				return "PDF Displays $this->guid";
			case 'type':
				return 'PDF display holder';
			case 'types':
				return 'PDF display holders';
			case 'icon':
				return 'picon-application-pdf';
		}
		return null;
	}

	/**
	 * Load the JavaScript to insert display editors into the page.
	 *
	 * This adds additional functions to any pform fields in an element div with
	 * the 'display_edit' class.
	 */
	public function load_editors() {
		$this->page_count();
		$module = new module('com_pdf', 'editors', 'head');
		$module->entity = $this;
	}

	/**
	 * Update and return the page count.
	 *
	 * @return int The page count of the current PDF.
	 */
	public function page_count() {
		global $_;
		require_once('components/com_pdf/includes/tcpdf/tcpdf.php');
		require_once('components/com_pdf/includes/fpdi/fpdi.php');
		$pdf = new FPDI();
		$this->pdf_pages = $pdf->setSourceFile($_->config->com_pdf->pdf_path.clean_filename($this->pdf_file));
		return $this->pdf_pages;
	}

	/**
	 * Read the data provided by the user into the display values.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function read_request_data() {
		if (empty($_REQUEST))
			return false;
		$this->displays = array();
		foreach ($_REQUEST as $cur_key => $cur_value) {
			if (preg_match('/_displays_json$/', $cur_key)) {
				$cur_key = preg_replace('/_displays_json$/', '', $cur_key);
				$this->displays[$cur_key] = json_decode($cur_value);
				if (!is_array($this->displays[$cur_key]))
					$this->displays[$cur_key] = array();
			}
		}
		return true;
	}

	/**
	 * Render the PDF using the provided data.
	 *
	 * @param mixed $entity An entity with the data to be placed on the page.
	 * @param bool $print If true, the PDF is output to the client as a download.
	 * @return string The generated PDF contents.
	 */
	public function render($entity, $print = true) {
		global $_;
		require_once('components/com_pdf/includes/tcpdf/tcpdf.php');
		require_once('components/com_pdf/includes/fpdi/fpdi.php');

		$pdf = new FPDI();

		// remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		if (isset($this->pdf_title)) {
			$pdf->SetTitle($this->pdf_title);
		}
		if (isset($this->pdf_author)) {
			$pdf->SetAuthor($this->pdf_author);
		}
		if (isset($this->pdf_creator)) {
			$pdf->SetCreator($this->pdf_creator);
		}
		if (isset($this->pdf_subject)) {
			$pdf->SetSubject($this->pdf_subject);
		}
		if (isset($this->pdf_keywords)) {
			$pdf->SetKeywords($this->pdf_keywords);
		}

		// Order the displays by page, and get the content to be printed.
		$displays = array();
		foreach ($this->displays as $cur_key => $cur_value) {
			if (!is_array($cur_value))
				continue;
			foreach ($cur_value as $cur_display) {
				$new_display = clone $cur_display;
				$new_display->content = $entity->$cur_key;
				$displays[$cur_display->page][] = $new_display;
			}
		}

		$pagecount = $pdf->setSourceFile($_->config->com_pdf->pdf_path.clean_filename($this->pdf_file));
		// Go through each display.
		for ($i = 1; $i <= $pagecount; $i++) {
			$tplidx = $pdf->importPage($i);
			$s = $pdf->getTemplatesize($tplidx);
			$pdf->AddPage('P', array($s['w'], $s['h']));
			$pdf->useTemplate($tplidx);
			$pdf->SetLineWidth(0);

			// Print each display onto the page.
			if (is_array($displays[$i])) {
				foreach ($displays[$i] as $cur_display) {
					//$pdf->AddFont(str_replace('Times New Roman', 'times', $cur_display->fontfamily));
					//$pdf->SetFont(str_replace('Times New Roman', 'times', $cur_display->fontfamily));
					//$pdf->SetXY(($cur_display->left * $s['w']), ($cur_display->top * $s['h']));
					//$html = "<span dir=\"$cur_display->direction\" style=\"overflow: $cur_display->overflow; font-weight: ".($cur_display->bold ? 'bold' : 'normal')."; font-style: ".($cur_display->italic ? 'italic' : 'normal')."; font-family: $cur_display->fontfamily; font-size: ".((1.320833333*$cur_display->fontsize)+0.470833345)."pt; color: $cur_display->fontcolor; letter-spacing: $cur_display->letterspacing; word-spacing: $cur_display->wordspacing; text-align: $cur_display->textalign; text-decoration: $cur_display->textdecoration;\">";
					//$font_size = ((1.320833333*$cur_display->fontsize)+0.470833345); // Too rough.
					$font_size = ((1.3806*$cur_display->fontsize)-1.1284); // Better.
					$font_style = ($cur_display->bold ? 'B' : '').($cur_display->italic ? 'I' : '').($cur_display->textdecoration == 'underline' ? 'U' : '').($cur_display->textdecoration == 'line-through' ? 'D' : '');
					$pdf->SetFont($cur_display->fontfamily, $font_style, $font_size);
					$font_color = $pdf->convertHTMLColorToDec($cur_display->fontcolor);
					if ($font_color)
						$pdf->SetTextColor($font_color['R'], $font_color['G'], $font_color['B']);
					//$cur_display->letterspacing // These aren't supported by TCPDF yet.
					//$cur_display->wordspacing
					switch ($cur_display->textalign) {
						case 'left':
							$align = 'L';
							break;
						case 'right':
							$align = 'R';
							break;
						case 'center':
							$align = 'C';
							break;
						case 'justify':
							$align = 'J';
							break;
					}
					switch ($cur_display->texttransform) {
						case 'capitalize':
							$content = ucwords($cur_display->content);
							break;
						case 'uppercase':
							$content = strtoupper($cur_display->content);
							break;
						case 'lowercase':
							$content = strtolower($cur_display->content);
							break;
						default:
							$content = $cur_display->content;
							break;
					}
					if ($cur_display->addspacing)
						$content = substr(preg_replace('/\b|\B/', ' ', $content), 1, -1);
					//$html .= "</span>";
					if ($cur_display->direction == 'ltr') {
						$pdf->setTempRTL('L');
					} elseif ($cur_display->direction == 'rtl') {
						$pdf->setTempRTL('R');
					} else {
						$pdf->setTempRTL(false);
					}
					$width = ($cur_display->width * $s['w']);
					$height = ($cur_display->height * $s['h']);
					$x = ($cur_display->left * $s['w']);
					$y = ($cur_display->top * $s['h']);
					$maxh = ($cur_display->overflow ? 0 : $height);
					$border = ($cur_display->border ? 1 : 0);
					$pdf->MultiCell($width, $height, $content, $border, $align, 0, 1, $x, $y, true, 0, false, false, $maxh);
					//$pdf->writeHTMLCell(($cur_display->width * $s['w']), ($cur_display->height * $s['h']), ($cur_display->left * $s['w']), ($cur_display->top * $s['h']), $html, 0, 1, 0, true);
					//$pdf->Cell(($cur_display->width * $s['w']), ($cur_display->height * $s['h']), $cur_display->content);
				}
			}
		}

		// Get the generated PDF.
		$output = $pdf->Output($this->pdf_title, 'S');
		if ($print) {
			// Print it out to the user.
			global $_;
			$_->page->override = true;
			header('Content-type: application/pdf');
			header('Content-Disposition: attachment; filename="'.$this->pdf_dl_filename.'" size='.strlen($output));
			$_->page->override_doc($output);
		}
		return $output;
	}
}