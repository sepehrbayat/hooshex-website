<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('seoAnalyzer', () => ({
        title: '',
        content: '',
        metaDescription: '',
        focusKeyword: '',
        allKeywords: [],
        slug: '',
        score: 0,
        readabilityScore: 0,
        checks: {},
        initialized: false,
        
        init() {
            console.log('[SEO Analyzer] Initializing...');
            this.watchFields();
        },
        
        watchFields() {
            const selectors = {
                title: [
                    'input[wire\\:model\\.live\\.onBlur="data.title"]',
                    'input[wire\\:model\\.live="data.title"]',
                    'input[wire\\:model="data.title"]',
                    '[id$="data.title"]'
                ],
                slug: [
                    'input[wire\\:model\\.live\\.onBlur="data.slug"]',
                    'input[wire\\:model\\.live="data.slug"]',
                    'input[wire\\:model="data.slug"]',
                    '[id$="data.slug"]'
                ],
                description: [
                    'textarea[wire\\:model\\.live="data.seo.description"]',
                    'textarea[wire\\:model="data.seo.description"]',
                    'input[wire\\:model="data.seo.description"]',
                    '[id$="data.seo.description"]'
                ],
                focusKeyword: [
                    'input[wire\\:model\\.live="data.focus_keywords"]',
                    'input[wire\\:model="data.focus_keywords"]',
                    '[id$="data.focus_keywords"]',
                    '[data-field="focus_keywords"] input',
                    'input[name*="focus_keywords"]'
                ]
            };
            
            const findElement = (selectorList, fieldName) => {
                for (const selector of selectorList) {
                    try {
                        const el = document.querySelector(selector);
                        if (el) {
                            console.log('[SEO Analyzer] Found ' + fieldName + ':', selector);
                            return el;
                        }
                    } catch (e) {
                        // Selector might be invalid
                    }
                }
                // Fallback: search by partial id match
                const allInputs = document.querySelectorAll('input, textarea');
                for (const input of allInputs) {
                    if (input.id && (input.id.includes(fieldName.replace('_', '.')) || input.name === fieldName)) {
                        console.log('[SEO Analyzer] Found ' + fieldName + ' by id/name:', input.id || input.name);
                        return input;
                    }
                }
                return null;
            };
            
            const setupInterval = setInterval(() => {
                const titleInput = findElement(selectors.title, 'title');
                const slugInput = findElement(selectors.slug, 'slug');
                const descInput = findElement(selectors.description, 'seo.description');
                const keywordInput = findElement(selectors.focusKeyword, 'focus_keyword');
                
                // Find Trix Editor
                const trixEditor = document.querySelector('trix-editor');
                
                if (titleInput || trixEditor) {
                    clearInterval(setupInterval);
                    this.initialized = true;
                    console.log('[SEO Analyzer] Setup complete');
                    
                    // Title
                    if (titleInput) {
                        const updateTitle = () => {
                            this.title = titleInput.value || '';
                            this.updateAnalysis();
                        };
                        titleInput.addEventListener('input', updateTitle);
                        titleInput.addEventListener('change', updateTitle);
                        titleInput.addEventListener('blur', updateTitle);
                        this.title = titleInput.value || '';
                    }
                    
                    // Slug
                    if (slugInput) {
                        const updateSlug = () => {
                            this.slug = slugInput.value || '';
                            this.updateAnalysis();
                        };
                        slugInput.addEventListener('input', updateSlug);
                        slugInput.addEventListener('change', updateSlug);
                        this.slug = slugInput.value || '';
                    }
                    
                    // Meta Description
                    if (descInput) {
                        const updateDesc = () => {
                            this.metaDescription = descInput.value || '';
                            this.updateAnalysis();
                        };
                        descInput.addEventListener('input', updateDesc);
                        descInput.addEventListener('change', updateDesc);
                        this.metaDescription = descInput.value || '';
                    }
                    
                    // Focus Keywords - Use new multi-strategy approach
                    this.setupKeywordWatcher();
                    
                    // Trix Editor for content
                    if (trixEditor) {
                        const updateContent = () => {
                            const inputId = trixEditor.getAttribute('input');
                            const hiddenInput = document.getElementById(inputId);
                            this.content = hiddenInput ? hiddenInput.value : trixEditor.innerHTML;
                            this.updateAnalysis();
                        };
                        trixEditor.addEventListener('trix-change', updateContent);
                        trixEditor.addEventListener('trix-blur', updateContent);
                        // Initial content
                        const inputId = trixEditor.getAttribute('input');
                        const hiddenInput = document.getElementById(inputId);
                        this.content = hiddenInput ? hiddenInput.value : '';
                    }
                    
                    this.updateAnalysis();
                }
            }, 500);
            
            // Stop after 15 seconds
            setTimeout(() => {
                clearInterval(setupInterval);
                if (!this.initialized) {
                    console.warn('[SEO Analyzer] Could not find form fields after 15 seconds');
                }
            }, 15000);
        },
        
        setupKeywordWatcher() {
            const updateKeywords = () => {
                let keywords = null;
                
                // Direct Livewire data access (primary strategy)
                if (window.Livewire && this.$wire && this.$wire.data && this.$wire.data.focus_keywords) {
                    keywords = this.$wire.data.focus_keywords;
                }
                
                if (keywords) {
                    try {
                        if (Array.isArray(keywords)) {
                            this.focusKeyword = keywords[0] || '';
                            this.allKeywords = keywords; // Store all keywords
                        } else if (typeof keywords === 'string') {
                            if (keywords.startsWith('[')) {
                                const parsed = JSON.parse(keywords);
                                if (Array.isArray(parsed)) {
                                    this.focusKeyword = parsed[0] || '';
                                    this.allKeywords = parsed;
                                } else {
                                    this.focusKeyword = keywords;
                                    this.allKeywords = [keywords];
                                }
                            } else {
                                const keywordArray = keywords.split(',').map(k => k.trim()).filter(k => k);
                                this.focusKeyword = keywordArray[0] || '';
                                this.allKeywords = keywordArray;
                            }
                        }
                        
                        this.updateAnalysis();
                    } catch (e) {
                        console.error('[SEO Analyzer] Error parsing keywords:', e);
                    }
                } else {
                    this.focusKeyword = '';
                    this.allKeywords = [];
                }
            };
            
            // Initial update
            updateKeywords();
            
            // Set up periodic checking for live updates
            const keywordInterval = setInterval(updateKeywords, 1000);
            
            // Clean up after 30 seconds
            setTimeout(() => clearInterval(keywordInterval), 30000);
        },
        
        updateAnalysis() {
            this.performChecks();
            this.calculateScore();
            this.calculateReadability();
        },
        
        getPlainText(html) {
            if (!html) return '';
            const temp = document.createElement('div');
            temp.innerHTML = html;
            return temp.textContent || temp.innerText || '';
        },
        
        performChecks() {
            const plainContent = this.getPlainText(this.content);
            const words = plainContent.split(/\s+/).filter(w => w.length > 0);
            const wordCount = words.length;
            const keyword = this.focusKeyword.toLowerCase().trim();
            const hasKeyword = keyword.length > 0;
            
            this.checks = {
                // Basic SEO
                hasFocusKeyword: hasKeyword,
                titleLength: this.title.length >= 30 && this.title.length <= 60,
                keywordInTitle: hasKeyword && this.title.toLowerCase().includes(keyword),
                keywordInMeta: hasKeyword && this.metaDescription.toLowerCase().includes(keyword),
                keywordInUrl: hasKeyword && this.checkKeywordInUrl(keyword),
                keywordAtStart: hasKeyword && this.checkKeywordInFirstParagraph(keyword, plainContent),
                keywordInContent: hasKeyword && plainContent.toLowerCase().includes(keyword),
                contentLength: wordCount >= 600,
                metaLength: this.metaDescription.length >= 120 && this.metaDescription.length <= 160,
                
                // Additional - Updated to use all keywords for density
                keywordInHeadings: this.checkKeywordInHeadings(keyword),
                imageWithAlt: this.checkImageAlt(keyword),
                keywordDensity: this.calculateKeywordDensity(this.allKeywords, wordCount),
                shortUrl: this.slug.length > 0 && this.slug.length <= 75 && !this.slug.includes('%') && this.slug.split('-').length <= 5,
                externalLinks: this.checkExternalLinks(),
                doFollowExternalLinks: this.checkDoFollowExternalLinks(),
                internalLinks: this.checkInternalLinks(),
                hasTableOfContents: this.content.includes('فهرست') || this.content.toLowerCase().includes('table of content'),
                hasMedia: this.checkHasMedia(),
                
                // Title Readability  
                keywordNearStart: this.checkKeywordNearTitleStart(keyword),
                titleHasNumber: !/\d+/.test(this.title), // Rank Math: Error when NO number in title
                
                // Content Readability
                shortParagraphs: this.checkParagraphLength()
            };
        },
        
        checkKeywordInHeadings(keyword) {
            console.log('[SEO Analyzer] checkKeywordInHeadings called with:', keyword);
            if (!keyword) {
                console.log('[SEO Analyzer] No keyword provided');
                return false;
            }
            
            const keywordLower = keyword.toLowerCase();
            console.log('[SEO Analyzer] Looking for keyword in headings:', keywordLower);
            
            // Split keyword into individual words (remove common stop words)
            const stopWords = ['از', 'به', 'در', 'تا', 'برای', 'با', 'و', 'که', 'این', 'آن', 'را', 'است', 'چه', 'چگونه', 'کجا'];
            const keywordWords = keywordLower.split(/\s+/)
                .filter(word => word.length > 2 && !stopWords.includes(word));
            
            console.log('[SEO Analyzer] Keyword words to match:', keywordWords);
            console.log('[SEO Analyzer] Content to search:', this.content.substring(0, 200) + '...');
            
            // Rank Math priority: H1 > H2 > H3 > H4-H6
            // Check for all heading levels with improved regex
            const headingPatterns = [
                // H1 - Highest priority
                /<h1[^>]*>([\s\S]*?)<\/h1>/gi,
                // H2, H3 - High priority  
                /<h2[^>]*>([\s\S]*?)<\/h2>/gi,
                /<h3[^>]*>([\s\S]*?)<\/h3>/gi,
                // H4-H6 - Lower priority but still important
                /<h4[^>]*>([\s\S]*?)<\/h4>/gi,
                /<h5[^>]*>([\s\S]*?)<\/h5>/gi,
                /<h6[^>]*>([\s\S]*?)<\/h6>/gi
            ];
            
            // Also check for RichEditor heading formatting
            const richEditorPatterns = [
                /<div[^>]*data-type="heading"[^>]*level="1"[^>]*>([\s\S]*?)<\/div>/gi,
                /<div[^>]*data-type="heading"[^>]*level="2"[^>]*>([\s\S]*?)<\/div>/gi,
                /<div[^>]*data-type="heading"[^>]*level="3"[^>]*>([\s\S]*?)<\/div>/gi,
                // Check for div with heading classes
                /<div[^>]*class="[^"]*\bh1\b[^"]*"[^>]*>([\s\S]*?)<\/div>/gi,
                /<div[^>]*class="[^"]*\bh2\b[^"]*"[^>]*>([\s\S]*?)<\/div>/gi,
                /<div[^>]*class="[^"]*\bh3\b[^"]*"[^>]*>([\s\S]*?)<\/div>/gi
            ];
            
            const allPatterns = [...headingPatterns, ...richEditorPatterns];
            console.log('[SEO Analyzer] Testing', allPatterns.length, 'patterns');
            
            let foundHeadings = [];
            
            // Check each pattern
            for (let i = 0; i < allPatterns.length; i++) {
                const pattern = allPatterns[i];
                let match;
                // Reset regex lastIndex for each pattern
                pattern.lastIndex = 0;
                
                while ((match = pattern.exec(this.content)) !== null) {
                    const headingText = this.getPlainText(match[1]).toLowerCase();
                    console.log('[SEO Analyzer] Found heading:', headingText);
                    foundHeadings.push(headingText);
                    
                    // Check if ALL major keywords are found in this heading or content
                    // OR check if heading contains significant keyword words
                    let matchedWords = 0;
                    for (const word of keywordWords) {
                        if (headingText.includes(word)) {
                            matchedWords++;
                        }
                    }
                    
                    // If at least 50% of important keywords found, consider it a match
                    const matchRatio = matchedWords / keywordWords.length;
                    console.log('[SEO Analyzer] Matched words:', matchedWords, '/', keywordWords.length, '=', Math.round(matchRatio * 100) + '%');
                    
                    if (matchRatio >= 0.4) { // 40% threshold - more flexible than exact match
                        console.log('[SEO Analyzer] ✅ Sufficient keyword match found in heading!');
                        return true;
                    }
                    
                    // Prevent infinite loops
                    if (!pattern.global) break;
                }
            }
            
            console.log('[SEO Analyzer] All found headings:', foundHeadings);
            console.log('[SEO Analyzer] ❌ Insufficient keyword matches in headings');
            return false;
        },
        
        checkImageAlt(keyword) {
            if (!keyword) return false;
            const imgRegex = /<img[^>]+alt\s*=\s*["']([^"']*)["'][^>]*>/gi;
            let match;
            while ((match = imgRegex.exec(this.content)) !== null) {
                if (match[1].toLowerCase().includes(keyword)) {
                    return true;
                }
            }
            return false;
        },
        
        calculateKeywordDensity(keywords, wordCount) {
            if (!keywords || !Array.isArray(keywords) || keywords.length === 0 || wordCount === 0) return false;
            const plainContent = this.getPlainText(this.content).toLowerCase();
            
            console.log('[SEO Analyzer] Density calculation - Keywords:', keywords);
            console.log('[SEO Analyzer] Density calculation - Word count:', wordCount);
            
            // Track unique match positions to avoid overlapping counts
            const matchedPositions = new Set();
            let totalUniqueMatches = 0;
            
            // Sort keywords by length (longest first) to prioritize longer matches
            const sortedKeywords = keywords
                .filter(keyword => keyword && keyword.trim())
                .sort((a, b) => b.length - a.length);
            
            console.log('[SEO Analyzer] Sorted keywords (longest first):', sortedKeywords);
            
            // Count matches for all keywords without overlapping
            sortedKeywords.forEach(keyword => {
                if (keyword && keyword.trim()) {
                    const escapedKeyword = keyword.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const keywordRegex = new RegExp(escapedKeyword, 'gi');
                    let match;
                    let keywordMatches = 0;
                    
                    while ((match = keywordRegex.exec(plainContent)) !== null) {
                        const startPos = match.index;
                        const endPos = match.index + match[0].length;
                        
                        // Check if this position overlaps with any existing match
                        let overlaps = false;
                        for (let i = startPos; i < endPos; i++) {
                            if (matchedPositions.has(i)) {
                                overlaps = true;
                                break;
                            }
                        }
                        
                        // If no overlap, count this match and mark positions
                        if (!overlaps) {
                            for (let i = startPos; i < endPos; i++) {
                                matchedPositions.add(i);
                            }
                            keywordMatches++;
                            totalUniqueMatches++;
                        }
                    }
                    
                    console.log('[SEO Analyzer] Keyword:', keyword, '- Unique matches:', keywordMatches);
                }
            });
            
            const density = (totalUniqueMatches / wordCount) * 100;
            console.log('[SEO Analyzer] Total unique matches:', totalUniqueMatches, '- Density:', density.toFixed(2) + '%');
            
            // Rank Math: Optimal keyword density is 0.8% - 1.3%
            return density >= 0.8 && density <= 1.3;
        },
        
        getKeywordDensityPercentage(keywords, wordCount) {
            if (!keywords || !Array.isArray(keywords) || keywords.length === 0 || wordCount === 0) return 0;
            const plainContent = this.getPlainText(this.content).toLowerCase();
            
            // Track unique match positions to avoid overlapping counts
            const matchedPositions = new Set();
            let totalUniqueMatches = 0;
            
            // Sort keywords by length (longest first) to prioritize longer matches
            const sortedKeywords = keywords
                .filter(keyword => keyword && keyword.trim())
                .sort((a, b) => b.length - a.length);
            
            // Count matches for all keywords without overlapping
            sortedKeywords.forEach(keyword => {
                if (keyword && keyword.trim()) {
                    const escapedKeyword = keyword.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const keywordRegex = new RegExp(escapedKeyword, 'gi');
                    let match;
                    
                    while ((match = keywordRegex.exec(plainContent)) !== null) {
                        const startPos = match.index;
                        const endPos = match.index + match[0].length;
                        
                        // Check if this position overlaps with any existing match
                        let overlaps = false;
                        for (let i = startPos; i < endPos; i++) {
                            if (matchedPositions.has(i)) {
                                overlaps = true;
                                break;
                            }
                        }
                        
                        // If no overlap, count this match and mark positions
                        if (!overlaps) {
                            for (let i = startPos; i < endPos; i++) {
                                matchedPositions.add(i);
                            }
                            totalUniqueMatches++;
                        }
                    }
                }
            });
            
            const density = (totalUniqueMatches / wordCount) * 100;
            return Math.round(density * 100) / 100; // Round to 2 decimal places
        },
        
        checkExternalLinks() {
            const linkRegex = /<a[^>]+href\s*=\s*["']([^"']*)["'][^>]*>/gi;
            let match;
            while ((match = linkRegex.exec(this.content)) !== null) {
                const href = match[1];
                if (href.startsWith('http') && !href.includes('hooshex')) {
                    return true;
                }
            }
            return false;
        },
        
        checkDoFollowExternalLinks() {
            const linkRegex = /<a[^>]*href\s*=\s*["']([^"']*)["'][^>]*>/gi;
            let match;
            while ((match = linkRegex.exec(this.content)) !== null) {
                const fullTag = match[0];
                const href = match[1];
                // Check if it's external and NOT nofollow
                if (href.startsWith('http') && !href.includes('hooshex')) {
                    if (!fullTag.toLowerCase().includes('nofollow')) {
                        return true;
                    }
                }
            }
            return false;
        },
        
        checkInternalLinks() {
            const linkRegex = /<a[^>]+href\s*=\s*["']([^"']*)["'][^>]*>/gi;
            let match;
            while ((match = linkRegex.exec(this.content)) !== null) {
                const href = match[1];
                if (href.startsWith('/') || href.includes('hooshex')) {
                    return true;
                }
            }
            return false;
        },
        
        checkKeywordNearTitleStart(keyword) {
            if (!keyword || this.title.length === 0) return false;
            const halfLength = Math.ceil(this.title.length / 2);
            const firstHalf = this.title.substring(0, halfLength).toLowerCase();
            return firstHalf.includes(keyword);
        },
        
        checkKeywordInFirstParagraph(keyword, plainContent) {
            if (!keyword) return false;
            // Rank Math checks if keyword appears in first 100-150 words (first paragraph)
            const words = plainContent.split(/\s+/).filter(w => w.length > 0);
            const firstWords = words.slice(0, 100).join(' ').toLowerCase();
            return firstWords.includes(keyword);
        },
        
        checkKeywordInUrl(keyword) {
            if (!keyword || !this.slug) return false;
            // Convert slug to readable format and check for keyword or its variations
            const urlText = this.slug.toLowerCase().replace(/-/g, ' ').replace(/_/g, ' ');
            // Check for exact match or keyword parts (Rank Math algorithm)
            const keywordParts = keyword.toLowerCase().split(/\s+/);
            return keywordParts.some(part => part.length > 2 && urlText.includes(part));
        },
        
        checkParagraphLength() {
            // Rank Math: Paragraphs should be under 150 words, ideally 50-75 words
            const paragraphs = this.content.split(/<\/p>|<br\s*\/?>/i).filter(p => {
                const text = this.getPlainText(p).trim();
                return text.length > 20;
            });
            
            if (paragraphs.length < 2) return true; // Not enough content to judge
            
            let goodParagraphs = 0;
            paragraphs.forEach(p => {
                const words = this.getPlainText(p).split(/\s+/).filter(w => w.length > 0);
                // Rank Math considers 50-75 words ideal, up to 150 acceptable
                if (words.length >= 10 && words.length <= 150) {
                    goodParagraphs++;
                }
            });
            
            return goodParagraphs >= paragraphs.length * 0.7; // 70% of paragraphs should be good
        },
        
        checkHasMedia() {
            // Rank Math: Check for images and videos
            const hasImages = /<img[^>]*>/gi.test(this.content);
            const hasVideos = /<video[^>]*>/gi.test(this.content) || /youtube|vimeo|video/gi.test(this.content);
            return hasImages || hasVideos;
        },
        
        calculateScore() {
            let score = 0;
            const totalPoints = 102; // Total possible points
            
            // Rank Math scoring system (weighted)
            const scoreChecks = [
                { test: this.checks.hasFocusKeyword, points: 15, critical: true }, // Most important
                { test: this.checks.keywordInTitle, points: 15, critical: true },
                { test: this.checks.keywordInContent, points: 10 },
                { test: this.checks.contentLength, points: 10 },
                { test: this.checks.keywordInMeta, points: 8 },
                { test: this.checks.keywordAtStart, points: 8 },
                { test: this.checks.titleLength, points: 7 },
                { test: this.checks.metaLength, points: 7 },
                { test: this.checks.keywordInUrl, points: 6 },
                { test: this.checks.keywordInHeadings, points: 6 },
                { test: this.checks.keywordDensity, points: 5 },
                { test: this.checks.internalLinks, points: 3 },
                { test: this.checks.externalLinks, points: 2 }
            ];
            
            scoreChecks.forEach(check => {
                if (check.test) score += check.points;
            });
            
            this.score = Math.min(100, Math.round((score / totalPoints) * 100));
        },
        
        calculateReadability() {
            const plainContent = this.getPlainText(this.content);
            const sentences = plainContent.split(/[.!?؟۔]+/).filter(s => s.trim().length > 5);
            const words = plainContent.split(/\s+/).filter(w => w.length > 0);
            
            let score = 70; // Base score
            
            // Rank Math readability factors
            if (sentences.length > 0 && words.length > 0) {
                const avgWordsPerSentence = words.length / sentences.length;
                
                // Sentence length analysis (Rank Math prefers 15-20 words per sentence)
                if (avgWordsPerSentence >= 15 && avgWordsPerSentence <= 20) {
                    score += 15;
                } else if (avgWordsPerSentence > 25) {
                    score -= 20;
                } else if (avgWordsPerSentence > 20) {
                    score -= 10;
                } else if (avgWordsPerSentence < 10) {
                    score -= 5;
                }
            }
            
            // Paragraph structure bonus
            if (this.checks.shortParagraphs) score += 15;
            
            // Content length factor
            const wordCount = words.length;
            if (wordCount >= 300 && wordCount <= 2500) {
                score += 10;
            } else if (wordCount > 2500) {
                score -= 5;
            }
            
            this.readabilityScore = Math.max(0, Math.min(100, score));
        },
        
        getScoreColor(score) {
            if (score >= 80) return 'text-green-600 dark:text-green-400';
            if (score >= 60) return 'text-yellow-600 dark:text-yellow-400';
            return 'text-red-600 dark:text-red-400';
        },
        
        getScoreBgColor(score) {
            if (score >= 80) return 'bg-green-100 dark:bg-green-900/30';
            if (score >= 60) return 'bg-yellow-100 dark:bg-yellow-900/30';
            return 'bg-red-100 dark:bg-red-900/30';
        },
        
        getScoreBorderColor(score) {
            if (score >= 80) return 'border-green-500';
            if (score >= 60) return 'border-yellow-500';
            return 'border-red-500';
        },
        
        getScoreLabel(score) {
            if (score >= 80) return 'عالی';
            if (score >= 60) return 'خوب';
            return 'نیاز به بهبود';
        },
        
        getWordCount() {
            return this.getPlainText(this.content).split(/\s+/).filter(w => w.length > 0).length;
        },
        
        getSentenceCount() {
            return this.getPlainText(this.content).split(/[.!?؟۔]+/).filter(s => s.trim().length > 5).length;
        },
        
        getParagraphCount() {
            const paragraphs = this.content.split(/<\/p>/i).filter(p => this.getPlainText(p).trim().length > 20);
            return paragraphs.length || 1;
        },
        
        getCurrentDensity() {
            if (!this.allKeywords || this.allKeywords.length === 0) return '0.0';
            const plainContent = this.getPlainText(this.content);
            const wordCount = plainContent.split(/\s+/).filter(w => w.length > 0).length;
            return this.getKeywordDensityPercentage(this.allKeywords, wordCount);
        },
        
        getCurrentUrlLength() {
            return this.slug ? this.slug.length : 0;
        }
    }));
});
</script>

<div class="space-y-4" x-data="seoAnalyzer()">
    
    <!-- Score Cards -->
    <div class="grid grid-cols-2 gap-4">
        <!-- SEO Score -->
        <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 p-5 border-2 transition-all duration-200" 
             :class="getScoreBorderColor(score)">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" 
                       :class="getScoreColor(score)">امتیاز SEO</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black" :class="getScoreColor(score)" x-text="score"></span>
                        <span class="text-lg font-semibold text-gray-400">/100</span>
                    </div>
                </div>
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center" 
                     :class="getScoreBgColor(score)">
                    <svg class="w-8 h-8" :class="getScoreColor(score)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold" 
                      :class="getScoreColor(score) + ' ' + getScoreBgColor(score)" 
                      x-text="getScoreLabel(score)"></span>
            </div>
        </div>
        
        <!-- Readability Score -->
        <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 p-5 border-2 transition-all duration-200" 
             :class="readabilityScore >= 80 ? 'border-purple-500' : readabilityScore >= 60 ? 'border-yellow-500' : 'border-red-500'">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1 text-purple-600 dark:text-purple-400">خوانایی</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black" :class="getScoreColor(readabilityScore)" x-text="readabilityScore"></span>
                        <span class="text-lg font-semibold text-gray-400">/100</span>
                    </div>
                </div>
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center bg-purple-100 dark:bg-purple-900/30">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold" 
                      :class="getScoreColor(readabilityScore) + ' ' + getScoreBgColor(readabilityScore)" 
                      x-text="getScoreLabel(readabilityScore)"></span>
            </div>
        </div>
    </div>
    
    <!-- Analysis Details with Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <div x-data="{ activeTab: 'basic' }" class="space-y-4">
            <!-- Tab Headers -->
            <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
                <button @click="activeTab = 'basic'" 
                        :class="activeTab === 'basic' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-600 dark:text-gray-400'" 
                        class="px-4 py-2 border-b-2 font-semibold text-sm transition-colors whitespace-nowrap">
                    Basic SEO
                </button>
                <button @click="activeTab = 'additional'" 
                        :class="activeTab === 'additional' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-600 dark:text-gray-400'" 
                        class="px-4 py-2 border-b-2 font-semibold text-sm transition-colors whitespace-nowrap">
                    Additional
                </button>
                <button @click="activeTab = 'title'" 
                        :class="activeTab === 'title' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-600 dark:text-gray-400'" 
                        class="px-4 py-2 border-b-2 font-semibold text-sm transition-colors whitespace-nowrap">
                    Title Readability
                </button>
                <button @click="activeTab = 'readability'" 
                        :class="activeTab === 'readability' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-600 dark:text-gray-400'" 
                        class="px-4 py-2 border-b-2 font-semibold text-sm transition-colors whitespace-nowrap">
                    Content Readability
                </button>
            </div>
            
            <!-- Basic SEO Tab -->
            <div x-show="activeTab === 'basic'" class="space-y-2">
                <template x-for="check in [
                    { key: 'keywordInTitle', label: 'Focus keyword used in SEO title' },
                    { key: 'keywordInMeta', label: 'Focus keyword used in meta description' },
                    { key: 'keywordInUrl', label: 'Focus keyword used in URL' },
                    { key: 'keywordAtStart', label: 'Focus keyword used at the beginning of content' },
                    { key: 'keywordInContent', label: 'Focus keyword used in content' },
                    { key: 'contentLength', label: 'Content should be 600+ words long' }
                ]">
                    <div class="flex items-center gap-3 p-3 rounded-lg transition-all" 
                         :class="checks[check.key] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'">
                        <template x-if="checks[check.key]">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </template>
                        <template x-if="!checks[check.key]">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </template>
                        <p class="text-sm font-medium" 
                           :class="checks[check.key] ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'" 
                           x-text="check.label"></p>
                    </div>
                </template>
            </div>
            
            <!-- Additional Tab -->
            <div x-show="activeTab === 'additional'" class="space-y-2">
                <template x-for="check in [
                    { key: 'keywordInHeadings', label: 'Focus keyword found in heading' },
                    { key: 'imageWithAlt', label: 'Focus keyword found in image alt attributes' },
                    { key: 'keywordDensity', label: 'Focus keyword density', showDensity: true },
                    { key: 'shortUrl', label: 'URL should be less than 75 characters', showUrlLength: true },
                    { key: 'externalLinks', label: 'Link to external resource added' },
                    { key: 'doFollowExternalLinks', label: 'Link to external DoFollow resource added' },
                    { key: 'internalLinks', label: 'Add internal links to older posts for better user experience' },
                    { key: 'hasFocusKeyword', label: 'Add focus keyword for this page' }
                ]">
                    <div class="flex items-center gap-3 p-3 rounded-lg transition-all" 
                         :class="checks[check.key] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20'">
                        <template x-if="checks[check.key]">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </template>
                        <template x-if="!checks[check.key]">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </template>
                        <p class="text-sm font-medium" 
                           :class="checks[check.key] ? 'text-green-800 dark:text-green-200' : 'text-yellow-800 dark:text-yellow-200'">
                            <span x-text="check.label"></span>
                            <span x-show="check.showDensity && focusKeyword" 
                                  x-text="': ' + getCurrentDensity() + '% (optimal: 0.8%-1.3%)'"></span>
                            <span x-show="check.showDensity && !focusKeyword" 
                                  x-text="' (0.8%-1.3%)'"></span>
                            <span x-show="check.showUrlLength" 
                                  x-text="': ' + getCurrentUrlLength() + ' characters (max: 75)'"></span>
                        </p>
                    </div>
                </template>
            </div>
            
            <!-- Title Readability Tab -->
            <div x-show="activeTab === 'title'" class="space-y-2">
                <template x-for="check in [
                    { key: 'keywordNearStart', label: 'Focus keyword appears at the beginning of SEO title' },
                    { key: 'titleHasNumber', label: 'Add a number to your title to get more clicks' }
                ]">
                    <div class="flex items-center gap-3 p-3 rounded-lg transition-all" 
                         :class="checks[check.key] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-blue-50 dark:bg-blue-900/20'">
                        <template x-if="checks[check.key]">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </template>
                        <template x-if="!checks[check.key]">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </template>
                        <p class="text-sm font-medium" 
                           :class="checks[check.key] ? 'text-green-800 dark:text-green-200' : 'text-blue-800 dark:text-blue-200'" 
                           x-text="check.label"></p>
                    </div>
                </template>
            </div>
            
            <!-- Content Readability Tab -->
            <div x-show="activeTab === 'readability'" class="space-y-2">
                <template x-for="check in [
                    { key: 'hasTableOfContents', label: 'Use Table of Content to break-down your text.' },
                    { key: 'shortParagraphs', label: 'Add short and concise paragraphs for better readability and UX.' },
                    { key: 'hasMedia', label: 'Add a few images and/or videos to make your content appealing.' }
                ]">
                    <div class="flex items-center gap-3 p-3 rounded-lg transition-all" 
                         :class="checks[check.key] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20'">
                        <template x-if="checks[check.key]">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </template>
                        <template x-if="!checks[check.key]">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </template>
                        <p class="text-sm font-medium" 
                           :class="checks[check.key] ? 'text-green-800 dark:text-green-200' : 'text-yellow-800 dark:text-yellow-200'" 
                           x-text="check.label"></p>
                    </div>
                </template>
                
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-3 pt-3 border-t border-gray-200 dark:border-gray-700 mt-4">
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="getWordCount()"></p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">کلمات</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="getSentenceCount()"></p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">جملات</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="getParagraphCount()"></p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">پاراگراف</p>
                    </div>
                </div>
                
                <!-- Meta Description Length -->
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600 dark:text-gray-400">طول توضیحات متا:</span>
                        <span class="font-bold" :class="metaDescription.length >= 120 && metaDescription.length <= 160 ? 'text-green-600' : 'text-yellow-600'" x-text="metaDescription.length + ' کاراکتر'"></span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-300"
                             :class="metaDescription.length >= 120 && metaDescription.length <= 160 ? 'bg-green-500' : metaDescription.length > 160 ? 'bg-red-500' : 'bg-yellow-500'"
                             :style="'width: ' + Math.min(100, (metaDescription.length / 160) * 100) + '%'"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Debug Info (hidden in production) -->
    <div x-show="!initialized" class="text-center py-4 text-gray-500 text-sm">
        <p>در حال اتصال به فیلدهای فرم...</p>
    </div>
</div>
