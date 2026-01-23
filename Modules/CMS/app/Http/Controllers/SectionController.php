<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CMS\app\Models\SiteSection;
use Modules\CMS\app\Services\SectionService;

class SectionController extends Controller
{
    protected SectionService $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    /**
     * Homepage sections management
     */
    public function homepage(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.view');

        $sections = SectionService::getPageSections('home');
        $sectionData = SiteSection::with('translation')
            ->where('page_name', 'home')
            ->get()
            ->keyBy('section_name');

        $activeSection = $request->get('section', 'hero_banner');
        $langCode = $request->get('lang', 'en');

        return view('cms::admin.sections.homepage', compact('sections', 'sectionData', 'activeSection', 'langCode'));
    }

    /**
     * Edit specific section
     */
    public function editSection(string $section, Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.view');

        $config = SectionService::getConfig($section);
        if (!$config) {
            abort(404, 'Section not found');
        }

        $pageName = $request->get('page', 'home');
        $langCode = $request->get('lang', 'en');

        $sectionData = SiteSection::with(['translations'])
            ->where('section_name', $section)
            ->where('page_name', $pageName)
            ->first();

        $translation = $sectionData?->getTranslation($langCode);

        return view('cms::admin.sections.edit', compact('section', 'config', 'sectionData', 'translation', 'pageName', 'langCode'));
    }

    /**
     * Update section
     */
    public function updateSection(string $section, Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.edit');

        $config = SectionService::getConfig($section);
        if (!$config) {
            abort(404, 'Section not found');
        }

        // Validate
        $rules = $config['validation'] ?? [];
        $rules['section_status'] = 'nullable|boolean';
        $rules['lang_code'] = 'nullable|string|max:10';

        $request->validate($rules);

        $pageName = $request->input('page_name', 'home');
        $result = $this->sectionService->updateSection($request, $section, $pageName);

        if ($result['success']) {
            return redirect()
                ->route('admin.cms.sections.edit', ['section' => $section, 'page' => $pageName, 'lang' => $request->input('lang_code', 'en')])
                ->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    /**
     * Toggle section status via AJAX
     */
    public function toggleStatus(string $section, Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.edit');

        $pageName = $request->input('page', 'home');
        $result = $this->sectionService->toggleStatus($section, $pageName);

        return response()->json($result);
    }

    /**
     * About page sections
     */
    public function aboutPage(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.view');

        $sections = SectionService::getPageSections('about');
        $sectionData = SiteSection::with('translation')
            ->where('page_name', 'about')
            ->get()
            ->keyBy('section_name');

        $activeSection = $request->get('section', 'about_hero');
        $langCode = $request->get('lang', 'en');

        return view('cms::admin.sections.about', compact('sections', 'sectionData', 'activeSection', 'langCode'));
    }

    /**
     * Contact page sections
     */
    public function contactPage(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.view');

        $sections = SectionService::getPageSections('contact');
        $sectionData = SiteSection::with('translation')
            ->where('page_name', 'contact')
            ->get()
            ->keyBy('section_name');

        $activeSection = $request->get('section', 'contact_breadcrumb');
        $langCode = $request->get('lang', 'en');

        return view('cms::admin.sections.contact', compact('sections', 'sectionData', 'activeSection', 'langCode'));
    }

    /**
     * Menu page sections
     */
    public function menuPage(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.view');

        $sections = SectionService::getPageSections('menu');
        $sectionData = SiteSection::with('translation')
            ->where('page_name', 'menu')
            ->get()
            ->keyBy('section_name');

        $activeSection = $request->get('section', 'menu_breadcrumb');
        $langCode = $request->get('lang', 'en');

        return view('cms::admin.sections.menu', compact('sections', 'sectionData', 'activeSection', 'langCode'));
    }

    /**
     * Reservation page sections
     */
    public function reservationPage(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.view');

        $sections = SectionService::getPageSections('reservation');
        $sectionData = SiteSection::with('translation')
            ->where('page_name', 'reservation')
            ->get()
            ->keyBy('section_name');

        $activeSection = $request->get('section', 'reservation_breadcrumb');
        $langCode = $request->get('lang', 'en');

        return view('cms::admin.sections.reservation', compact('sections', 'sectionData', 'activeSection', 'langCode'));
    }

    /**
     * Service page sections
     */
    public function servicePage(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.view');

        $sections = SectionService::getPageSections('service');
        $sectionData = SiteSection::with('translation')
            ->where('page_name', 'service')
            ->get()
            ->keyBy('section_name');

        $activeSection = $request->get('section', 'service_breadcrumb');
        $langCode = $request->get('lang', 'en');

        return view('cms::admin.sections.service', compact('sections', 'sectionData', 'activeSection', 'langCode'));
    }
}
